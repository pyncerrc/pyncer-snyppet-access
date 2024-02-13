<?php
namespace Pyncer\Snyppet\Access\Component\Module\User;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractPostItemModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Validation\ValidatorInterface;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserValidator;
use Pyncer\Snyppet\Access\User\PasswordConfig;

use function Pyncer\String\nullify as pyncer_string_nullify;

use const PASSWORD_DEFAULT;

class PostUserItemModule extends AbstractPostItemModule
{
    private ?PasswordConfig $passwordConfig = null;
    private ?PasswordConfig $defaultPasswordConfig = null;

    public function getPasswordConfig(): ?PasswordConfig
    {
        if ($this->passwordConfig !== null) {
            return $this->passwordConfig;
        }

        if ($this->defaultPasswordConfig === null) {
            $config = null;

            $snyppetManager = $this->get(ID::SNYPPET);
            if ($snyppetManager->has('config')) {
                $config = $this->get(ID::config());
            }

            $this->defaultPasswordConfig = new PasswordConfig($config);
        }

        return $this->defaultPasswordConfig;
    }
    public function setPasswordConfig(?PasswordConfig $value): static
    {
        $this->passwordConfig = $value;
        return $this;
    }

    protected function getResponseItemData(ModelInterface $model): array
    {
        $data = parent::getResponseItemData($model);

        unset($data['password']);

        return $data;
    }

    protected function validateItemData(array $data): array
    {
        $passwordErrors = [];

        if ($this->getPasswordConfig()->getConfirmNew()) {
            $password = pyncer_string_nullify($data['password1'] ?? null);
            $password2 = pyncer_string_nullify($data['password2'] ?? null);

            if ($password2 !== null && $password === null) {
                $passwordErrors['password1'] = 'required';
            } elseif ($password !== null && $password2 === null) {
                $passwordErrors['password2'] = 'required';
            } elseif ($password !==  null &&
                $password2 !== null &&
                $password !== $password2
            ) {
                $passwordErrors['password1'] = 'mismatch';
            }
        } else {
            $password = pyncer_string_nullify($data['password'])
        }

        if ($password !== null && !$passwordErrors) {
            $passwordRule = $this->getPasswordConfig->getPasswordRule();

            if (!$passwordRule->isValid($password)) {
                $passwordErrors['password'] = $passwordRule->getError();
            } else {
                $password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );
            }
        }

        if ($passwordErrors) {
            $data['password'] = null;
        } else {
            $data['password'] = $password;
        }

        $validator = $this->forgeValidator();
        [$data, $errors] = $validator->validateData($data);

        $errors = array_merge($errors, $passwordErrors);

        if ($this->getPasswordConfig()->getConfirmNew() &&
            array_key_exists('password', $errors)
        ) {
            $errors['password1'] = $errors['password'];
            unset($errors['password']);
        }

        return [$data, $errors];
    }

    protected function forgeValidator(): ?ValidatorInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new UserValidator($connection);
    }

    protected function forgeMapper(): MapperInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new UserMapper($connection);
    }
}
