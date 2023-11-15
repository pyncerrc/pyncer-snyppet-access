<?php
namespace Pyncer\Snyppet\Access\User;

use DateTime;
use DateTimeZone;
use DateInterval;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Database\ConnectionTrait;
use Pyncer\Exception\InvalidArgumentException;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Snyppet\Access\Table\Token\TokenMapper;
use Pyncer\Snyppet\Access\Table\Token\TokenModel;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserModel;
use Pyncer\Snyppet\Access\User\UserGroup;
use Pyncer\Snyppet\Access\User\LoginMethod;
use Pyncer\Snyppet\Access\User\PasswordManager;

use const Pyncer\Snyppet\Access\USER_GUEST_ID as PYNCER_ACCESS_USER_GUEST_ID;
use const Pyncer\DATE_TIME_NOW as PYNCER_DATE_TIME_NOW;

class AccessManager
{
    use ConnectionTrait;

    protected ?UserModel $userModel = null;

    public function __construct(ConnectionInterface $connection)
    {
        $this->setConnection($connection);
    }

    public function loginWithEmail(string $email, string $password): bool
    {
        return $this->loginWithCredentials($email, $password, LoginMethod::EMAIL);
    }

    public function loginWithPhone(string $phone, string $password): bool
    {
        return $this->loginWithCredentials($phone, $password, LoginMethod::PHONE);
    }

    public function loginWithUsername(string $username, string $password): bool
    {
        return $this->loginWithCredentials($username, $password, LoginMethod::USERNAME);
    }

    public function loginWithCredentials(
        string $login,
        string $password,
        LoginMethod $loginMethod
    ): bool
    {
        $this->userModel = null;

        $login = trim($login);
        $password = trim($password);

        if ($login === '') {
            return false;
        }

        $userMapper = $this->forgeUserMapper();
        $userMapperQuery = $this->forgeUserMapperQuery();

        $userModel = match ($loginMethod) {
            LoginMethod::EMAIL => $userMapper->selectByEmail($login, $userMapperQuery),
            LoginMethod::USERNAME => $userMapper->selectByUsername($login, $userMapperQuery),
            LoginMethod::PHONE => $userMapper->selectByPhone($login, $userMapperQuery),
            default => $userMapper->selectByEmail($login, $userMapperQuery),
        };

        if (!$userModel || !$userModel->getEnabled() || $userModel->getDeleted()) {
            return false;
        }

        if ($userModel->getId() !== PYNCER_ACCESS_USER_GUEST_ID) {
            if ($password === '') {
                return false;
            }

            $passwordManager = new PasswordManager(
                $this->getConnection(),
                $userModel
            );

            if (!$passwordManager->verify($password)) {
                return false;
            }
        }

        $this->userModel = $userModel;
        return true;
    }

    public function loginWithToken(string $scheme, string $realm, string $token): bool
    {
        $this->userModel = null;

        $tokenMapper = new TokenMapper($this->getConnection());
        $tokenModel = $tokenMapper->selectByToken(
            $scheme,
            $realm,
            $token
        );

        if (!$tokenModel ||
            !$tokenModel->getUserid() ||
            $tokenModel->getExpirationDateTime() < PYNCER_DATE_TIME_NOW
        ) {
            return false;
        }

        return $this->loginWithUserId($tokenModel->getUserId());
    }

    public function loginWithUserId(int $userId): bool
    {
        $this->userModel = null;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                'User id must be greater than zero.'
            );
        }

        $userMapper = $this->forgeUserMapper();
        $userMapperQuery = $this->forgeUserMapperQuery();
        $userModel = $userMapper->selectById($userId, $userMapperQuery);

        if (!$userModel ||
            !$userModel->getEnabled() ||
            $userModel->getDeleted()
        ) {
            return false;
        }

        $this->userModel = $userModel;
        return true;
    }

    public function loginWithUserModel(UserModel $user): bool
    {
        $this->userModel = null;

        if (!$user->getEnabled()) {
            throw new InvalidArgumentException('User is not enabled.');
        }

        if ($user->getDeleted()) {
            throw new InvalidArgumentException('User is deleted.');
        }

        $this->userModel = $user;
        return true;
    }

    public function logout(): bool
    {
        if ($this->userModel &&
            $this->userModel->getGroup() !== UserGroup::GUEST
        ) {
            $this->userModel = null;
            return true;
        }

        return false;
    }

    /**
    * @return \Pyncer\Data\Mapper\MapperInterface
    */
    protected function forgeUserMapper(): MapperInterface
    {
        return new UserMapper($this->getConnection());
    }
    /**
    * @return \Pyncer\Data\MapperQuery\MapperQueryInterface
    */
    protected function forgeUserMapperQuery(): ?MapperQueryInterface
    {
        return null;
    }

    public function getUser(): UserModel
    {
        if ($this->userModel === null) {
            $userModel = $this->getGuestUser();

            if (!$userModel ||
                !$userModel->getEnabled() ||
                $userModel->getDeleted() ||
                $userModel->getGroup() !== UserGroup::GUEST
            ) {
                throw new UnexpectedValueException(
                    'Expected guest user model.'
                );
            }

            $this->userModel = $userModel;
        }

        return $this->userModel;
    }

    protected function getGuestUser(): UserModel
    {
        $userMapper = $this->forgeUserMapper();
        $userMapperQuery = $this->forgeUserMapperQuery();

        return $userMapper->selectById(
            PYNCER_ACCESS_USER_GUEST_ID,
            $userMapperQuery
        );
    }

    public function getUserId(): int
    {
        return $this->getUser()->getId();
    }

    public function isGuest(): bool
    {
        return ($this->getUser()->getGroup() === UserGroup::GUEST);
    }

    public function isUser(): bool
    {
        if ($this->isSuper() || $this->isAdmin()) {
            return true;
        }

        return ($this->getUser()->getGroup() === UserGroup::USER);
    }

    public function isAdmin(): bool
    {
        if ($this->isSuper()) {
            return true;
        }

        return ($this->getUser()->getGroup() === UserGroup::ADMIN);
    }

    public function isSuper(): bool
    {
        return ($this->getUser()->getGroup() === UserGroup::SUPER);
    }
}
