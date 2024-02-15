<?php
namespace Pyncer\Snyppet\Access\Component\Module\User\Password;

use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Access\Module\User\PatchUserItemModule;

use function Pyncer\String\nullify as pyncer_string_nullify;

class PatchPasswordItemModule extends PatchUserItemModule
{
    protected function getResponseItemData(ModelInterface $model): array
    {
        return [];
    }

    protected function getRequestItemKeys(): ?array
    {
        if ($this->getPasswordConfig()->confirmNew()) {
            return ['password1', 'password2'];
        }

        return ['password'];
    }

    protected function validateItemData(array $data): array
    {
        [$data, $errors] = $this->validateItemData($data);

        // Make passwords required
        if ($this->getPasswordConfig()->getConfirmNew()) {
            if (!array_key_exists('password1', $errors)) {
                $password1 = pyncer_string_nullify($data['password1'] ?? null);
                $password2 = pyncer_string_nullify($data['password2'] ?? null);

                if ($password1 === null) {
                    $errors['password1'] = 'required';
                }

                if ($password2 === null) {
                    $errors['password2'] = 'required';
                }
            }
        } elseif (!array_key_exists('password', $errors)) {
            $password1 = pyncer_string_nullify($data['password']);

            if ($password1 === null) {
                $errors['password'] = 'required';
            }
        }

        return [$data, $errors];
    }
}
