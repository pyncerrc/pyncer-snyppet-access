<?php
namespace Pyncer\Snyppet\Access\Component\Module\User;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractPostItemModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Validation\ValidatorInterface;
use Pyncer\Snyppet\Access\Component\Forge\User\PasswordConfigTrait;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserValidator;
use Pyncer\Snyppet\Access\User\PasswordConfig;

use function Pyncer\String\nullify as pyncer_string_nullify;

use const PASSWORD_DEFAULT;

class PostUserItemModule extends AbstractPostItemModule
{
    use PasswordConfigTrait;

    protected function getResponseItemData(ModelInterface $model): array
    {
        $data = parent::getResponseItemData($model);

        unset($data['password'], $data['password1'], $data['password2']);

        return $data;
    }

    protected function validateItemData(array $data): array
    {
        $passwordErrors = [];
        $passwordConfig = $this->getPasswordConfig();

        if ($this->insertPassword()) {
            if ($passwordConfig->getConfirmNew()) {
                $password1 = pyncer_string_nullify($data['password1'] ?? null);
                $password2 = pyncer_string_nullify($data['password2'] ?? null);
            } else {
                $password1 = pyncer_string_nullify($data['password']);
                $password2 = $password1;
            }

            if ($this->requirePassword()) {
                if ($password1 === null) {
                    $passwordErrors['password1'] = 'required';
                }

                if ($password2 === null) {
                    $passwordErrors['password2'] = 'required';
                }

                if (!$passwordErrors && $password1 !== $password2) {
                    $passwordErrors['password1'] = 'mismatch';
                }
            } else {
                if ($password2 !== null && $password1 === null) {
                    $passwordErrors['password1'] = 'required';
                } elseif ($password1 !== null && $password2 === null) {
                    $passwordErrors['password2'] = 'required';
                } elseif ($password1 !==  null &&
                    $password2 !== null &&
                    $password1 !== $password2
                ) {
                    $passwordErrors['password1'] = 'mismatch';
                }
            }

            if ($password1 !== null && !$passwordErrors) {
                $passwordRule = $passwordConfig->getValidationRule();

                if (!$passwordRule->isValid($password1)) {
                    $passwordErrors['password1'] = $passwordRule->getError();
                } else {
                    $password1 = $passwordRule->clean($password1);

                    $password1 = password_hash(
                        $password1,
                        PASSWORD_DEFAULT
                    );
                }
            }

            $passwordErrors = $this->normalizePasswordErrors($passwordErrors);

            if (!$passwordErrors) {
                $data['password'] = $password1;
            }
        }

        $validator = $this->forgeValidator();
        [$data, $errors] = $validator->validateData($data);

        $errors = array_merge($errors, $passwordErrors);

        return [$data, $errors];
    }

    protected function insertPassword(): bool
    {
        $keys = $this->getRequestItemKeys();

        if ($keys === null) {
            return true;
        }

        if ($this->getPasswordConfig()->getConfirmNew()) {
            return (
                in_array('password1', $keys) &&
                in_array('password2', $keys)
            );
        }

        return in_array('password', $keys);
    }

    protected function requirePassword(): bool
    {
        return false;
    }

    private function normalizePasswordErrors(array $errors): array
    {
        if ($this->getPasswordConfig()->getConfirmNew()) {
            return $errors;
        }

        if (array_key_exists('password1', $errors)) {
            $errors['password'] = $errors['password1'];
            unset($errors['password1']);
        }

        unset($errors['password2']);

        return $errors;
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
