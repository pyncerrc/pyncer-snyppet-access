<?php
namespace Pyncer\Snyppet\Access\Component\Module\User\Password;

use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Access\Component\Module\User\PatchUserItemModule;

use function Pyncer\String\nullify as pyncer_string_nullify;

class PatchPasswordItemModule extends PatchUserItemModule
{
    protected function getResponseItemData(ModelInterface $model): array
    {
        return [];
    }

    protected function getRequestItemKeys(): ?array
    {
        $keys = [];

        $passwordConfig = $this->getPasswordConfig();

        if ($passwordConfig->getConfirmNew()) {
            if ($passwordConfig->getConfirmOld()) {
                $keys = ['password_new1', 'password_new2', 'password_old'];
            } else {
                $keys = ['password1', 'password2'];
            }
        } elseif ($passwordConfig->getConfirmOld()) {
            $keys = ['password_new', 'password_old'];
        } else {
            $keys = ['password'];
        }

        return $keys;
    }

    protected function requirePassword(): bool
    {
        return true;
    }
}
