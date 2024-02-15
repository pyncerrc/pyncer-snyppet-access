<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Access\Table\User\UserModel;
use Pyncer\Snyppet\Access\Table\User\UserMapperQuery;

class UserMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'user';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new UserModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof UserModel);
    }

    public function isValidMapperQuery(MapperQueryInterface $mapperQuery): bool
    {
        return ($mapperQuery instanceof UserMapperQuery);
    }

    public function selectByEmail(
        string $email,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        return $this->selectByColumns(['email' => $email], $mapperQuery);
    }

    public function selectByPhone(
        string $phone,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        $model = $this->selectByColumns(['phone' => $phone], $mapperQuery);

        if ($model !== null) {
            return $model;
        }

        $cleanPhone = preg_replace('/[^\d\+]/', '', $phone);
        if ($phone !== $cleanPhone) {
            return $this->selectByColumns(['phone' => $cleanPhone], $mapperQuery);
        }

        return null;
    }

    public function selectByUsername(
        string $username,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        return $this->selectByColumns(['username' => $username], $mapperQuery);
    }
}
