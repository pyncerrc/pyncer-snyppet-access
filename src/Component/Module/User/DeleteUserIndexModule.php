<?php
namespace Pyncer\Snyppet\Access\Component\Module\User;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractDeleteIndexModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserMapperQuery;

class DeleteUserItemModule extends AbstractDeleteIndexModule
{
    protected function forgeMapper(): MapperInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new UserMapper($connection);
    }

    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new UserMapperQuery($connection, $this->request);
    }
}
