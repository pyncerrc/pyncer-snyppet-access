<?php
namespace Pyncer\Snyppet\Access\Component\Module\User;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractPatchItemModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Validation\ValidatorInterface;
use Pyncer\Snyppet\Access\Component\Forge\User\PasswordConfigTrait;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserMapperQuery;
use Pyncer\Snyppet\Access\Table\User\UserValidator;
use Pyncer\Snyppet\Access\User\PasswordConfig;

use function Pyncer\date_time as pyncer_date_time;
use function Pyncer\String\nullify as pyncer_string_nullify;

use const PASSWORD_DEFAULT;

class PatchUserItemModule extends AbstractPatchItemModule
{
    use PasswordConfigTrait;

    protected function getResponseItemData(ModelInterface $model): array
    {
        $data = parent::getResponseItemData($model);

        unset(
            $data['password'],
            $data['password1'],
            $data['password2'],
            $data['password_new'],
            $data['password_new1'],
            $data['password_new2'],
            $data['password_old'],
        );

        return $data;
    }

    protected function getRequiredItemData(): array
    {
        $data = parent::getRequiredItemData();

        $data['update_date_time'] = pyncer_date_time();

        return $data;
    }

    protected function validateItemData(array $data): array
    {
        $passwordErrors = [];
        $passwordConfig = $this->getPasswordConfig();

        if ($this->updatePassword()) {
            if ($passwordConfig->getConfirmNew()) {
                if ($passwordConfig->getConfirmOld()) {
                    $passwordNew1 = pyncer_string_nullify($data['password_new1']);
                    $passwordNew2 = pyncer_string_nullify($data['password_new2']);
                    $passwordOld = pyncer_string_nullify($data['password_old']);
                } else {
                    $passwordNew1 = pyncer_string_nullify($data['password1']);
                    $passwordNew2 = pyncer_string_nullify($data['password2']);
                    $passwordOld = null;
                }
            } elseif ($passwordConfig->getConfirmOld()) {
                $passwordNew1 = pyncer_string_nullify($data['password_new']);
                $passwordNew2 = $passwordNew1;
                $passwordOld = pyncer_string_nullify($data['password_old']);
            } else {
                $passwordNew1 = pyncer_string_nullify($data['password']);
                $passwordNew2 = $passwordNew1;
                $passwordOld = null;
            }

            if ($this->requirePassword()) {
                if ($passwordNew1 === null) {
                    $passwordErrors['password_new1'] = 'required';
                }

                if ($passwordNew2 === null) {
                    $passwordErrors['password_new2'] = 'required';
                }

                if (!$passwordErrors && $passwordNew1 !== $passwordNew2) {
                    $passwordErrors['password_new1'] = 'mismatch';
                }
            } else {
                if ($passwordNew2 !== null && $passwordNew1 === null) {
                    $passwordErrors['password_new1'] = 'required';
                } elseif ($passwordNew1 !== null && $passwordNew2 === null) {
                    $passwordErrors['password_new2'] = 'required';
                } elseif ($passwordNew1 !==  null &&
                    $passwordNew2 !== null &&
                    $passwordNew1 !== $passwordNew2
                ) {
                    $passwordErrors['password_new1'] = 'mismatch';
                }
            }

            if ($passwordNew1 !== null && !$passwordErrors) {
                $passwordRule = $passwordConfig->getValidationRule();

                if (!$passwordRule->isValid($passwordNew1)) {
                    $passwordErrors['password_new1'] = $passwordRule->getError();
                } else {
                    $passwordNew1 = $passwordRule->clean($passwordNew1);
                }
            }

            if ($passwordConfig->getConfirmOld() &&
                $this->modelData['password'] !== null &&
                !password_verify($passwordOld, $this->modelData['password'])
            ) {
                $passwordErrors['password_old'] = 'mismatch';
            }

            if ($passwordNew1 !== null && !$passwordErrors) {
                $passwordNew1 = password_hash(
                    $passwordNew1,
                    PASSWORD_DEFAULT
                );
            }

            $passwordErrors = $this->normalizePasswordErrors($passwordErrors);

            if (!$passwordErrors) {
                $data['password'] = $passwordNew1;
            }
        } elseif ($this->confirmPassword() &&
            $this->modelData['password'] !== null
        ) {
            $password = pyncer_string_nullify($data['password']);

            if ($password === null) {
                $passwordErrors['password'] = 'required';
            } elseif (!password_verify($password, $this->modelData['password'])) {
                $passwordErrors['password'] = 'mismatch';
            }
        }

        $validator = $this->forgeValidator();
        [$data, $errors] = $validator->validateData($data);

        $errors = array_merge($errors, $passwordErrors);

        return [$data, $errors];
    }

    protected function updatePassword(): bool
    {
        $keys = $this->getRequestItemKeys();
        $passwordConfig = $this->getPasswordConfig();

        if ($passwordConfig->getConfirmNew()) {
            if ($keys === null) {
                return true;
            }

            if ($passwordConfig->getConfirmOld()) {
                return (in_array('password_old', $keys) &&
                    in_array('password_new1', $keys) &&
                    in_array('password_new2', $keys)
                );
            }

            return (
                in_array('password1', $keys) &&
                in_array('password2', $keys)
            );
        }

        if ($passwordConfig->getConfirmOld()) {
            if ($keys === null) {
                return true;
            }

            return (
                in_array('password_old', $keys) &&
                in_array('password_new', $keys)
            );
        }

        if ($keys === null) {
            if ($data['password'] === $this->modelData['password']) {
                return false;
            }

            return true;
        }

        return in_array('password', $keys);
    }

    protected function requirePassword(): bool
    {
        return false;
    }

    protected function confirmPassword(): bool
    {
        return false;
    }

    private function normalizePasswordErrors(array $errors): array
    {
        $passwordConfig = $this->getPasswordConfig();

        if ($passwordConfig->getConfirmNew()) {
            if ($passwordConfig->getConfirmOld()) {
                return $errors;
            }

            if (array_key_exists('password_new1', $errors)) {
                $errors['password1'] = $errors['password_new1'];
                unset($errors['password_new1']);
            }

            if (array_key_exists('password_new2', $errors)) {
                $errors['password2'] = $errors['password_new2'];
                unset($errors['password_new2']);
            }

            unset($errors['password_old']);

            return $errors;
        }

        if ($passwordConfig->getConfirmOld()) {
            if (array_key_exists('password_new1', $errors)) {
                $errors['password_new'] = $errors['password_new1'];
                unset($errors['password_new1']);
            }

            unset($errors['password_new2']);

            return $errors;
        }

        if (array_key_exists('password_new1', $errors)) {
            $errors['password'] = $errors['password_new1'];
            unset($errors['password_new1']);
        }

        unset($errors['password_new2']);
        unset($errors['password_old']);

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

    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new UserMapperQuery($connection);
    }
}
