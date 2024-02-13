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
}
