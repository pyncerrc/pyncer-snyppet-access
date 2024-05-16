<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Snyppet\Access\Table\User\DataModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Mapper\MapperResultInterface;

class DataMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'user__data';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new DataModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof DataModel);
    }

    public function selectByKey(
        int $userId,
        string $key,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        return $this->selectByColumns(
            [
                'user_id' => $userId,
                'key' => $key,
            ],
            $mapperQuery,
        );
    }

    public function selectAllByKeys(
        int $userId,
        array $keys,
        ?MapperQueryInterface $mapperQuery = null
    ): MapperResultInterface
    {
        return $this->selectAllByColumns(
            [
                'user_id' => $userId,
                'key' => $keys,
            ],
            $mapperQuery,
        );
    }
}
