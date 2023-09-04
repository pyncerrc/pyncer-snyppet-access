<?php
namespace Pyncer\Snyppet\Access\Component\Module\User;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractPatchItemModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Validation\ValidatorInterface;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserMapperQuery;
use Pyncer\Snyppet\Access\Table\User\UserValidator;

use const PASSWORD_DEFAULT;

class PatchUserItemModule extends AbstractPatchItemModule
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

    protected function getRequestItemData(): array
    {
        $data = parent::getRequestItemData();

        if (array_key_exists('password', $data)) {
            $data['password'] = trim(strval($data['password']));

            if ($data['password'] !== '') {
                $data['password'] = password_hash(
                    $data['password'],
                    PASSWORD_DEFAULT
                );
            }
        }

        return $data;
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
        return new UserMapperQuery($connection, $this->request);
    }
}
