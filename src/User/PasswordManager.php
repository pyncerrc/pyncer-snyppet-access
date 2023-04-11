<?php
namespace Pyncer\Snyppet\Access\User;

use Pyncer\Database\ConnectionInterface;
use Pyncer\Database\ConnectionTrait;
use Pyncer\Snyppet\Access\Table\User\UserModel;
use Pyncer\Snyppet\Access\Table\User\UserMapper;

use function password_hash;
use function password_verify;

use const PASSWORD_DEFAULT;

class PasswordManager
{
    use ConnectionTrait;

    public function __construct(
        ConnectionInterface $connection,
        protected UserModel $userModel
    ) {
        $this->setConnection($connection);
    }

    public function verify(string $password): bool
    {
        $salt = $this->userModel->getPassword();

        if (password_verify($password, $salt)) {
            return true;
        }

        return false;
    }

    public function get(): string
    {
        return $this->userModel->getPassword();
    }

    public function set(string $password): static
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $this->userModel->setPassword($password);

        $userMapper = new UserMapper($this->getConnection());
        $userMapper->update($this->userModel);

        return $this;
    }
}
