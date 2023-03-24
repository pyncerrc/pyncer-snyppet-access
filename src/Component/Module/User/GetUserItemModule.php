<?php
namespace Pyncer\Snyppet\Access\Component\Module\User;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractGetItemModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Access\Table\User\UserMapper;

class GetUserItemModule extends AbstractGetItemModule
{
    protected function getResponseItemData(ModelInterface $model): array
    {
        $data = parent::getResponseItemData($model);

        if (array_key_exists('password', $data) &&
            $data['password'] !== null
        ) {
            $data['password'] = '';
        }

        return $data;
    }

    protected function forgeMapper(): MapperInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new UserMapper($connection);
    }
}
