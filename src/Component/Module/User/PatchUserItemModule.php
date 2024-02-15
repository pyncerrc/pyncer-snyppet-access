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

        unset($data['password'], $data['password1'], $data['password2']);

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
        $updatePassword = false;

        if ($this->getPasswordConfig()->getConfirmNew()) {
            $updatePassword = true;
        } else {
            $keys = $this->getRequestItemKeys();
            if ($keys === null) {
                if ($data['password'] !== $this->modelData['password']) {
                    $updatePassword = true;
                }
            } else {
                $updatePassword = in_array('password', $keys);
            }
        }

        $passwordErrors = [];

        if ($updatePassword) {
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
                $password = pyncer_string_nullify($data['password']);
            }

            if ($password !== null && !$passwordErrors) {
                $passwordRule = $this->getPasswordConfig->getValidationRule();

                if (!$passwordRule->isValid($password)) {
                    $passwordErrors['password'] = $passwordRule->getError();
                } else {
                    $password = $passwordRule->clean($password);

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

    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new UserMapperQuery($connection);
    }
}
