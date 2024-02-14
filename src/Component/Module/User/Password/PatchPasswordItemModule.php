<?php
namespace Pyncer\Snyppet\Access\Component\Module\User\Password;

use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Access\Module\User\PatchUserItemModule;

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
            $password = pyncer_string_nullify($data['password1'] ?? null);
            $password2 = pyncer_string_nullify($data['password2'] ?? null);

            if ($password === null) {
                $errors['password1'] = 'required';
            }

            if ($password2 === null) {
                $errors['password2'] = 'required';
            }
        } else {
            $password = pyncer_string_nullify($data['password']);

            if ($password === null) {
                $errors['password'] = 'required';
            }
        }

        return [$data, $errors];
    }
}
