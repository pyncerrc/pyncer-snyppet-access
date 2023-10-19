<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Snyppet\Access\User\Table\User\ValueModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;

class ValueMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'user__value';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new ValueModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof ValueModel);
    }

    public function selectAllPreloaded(
        int $userId,
        ?MapperQueryInterface $mapperQuery = null
    ): MapperResultInterface
    {
        return $this->selectAllByColumns(
            [
                'user_id' => $userId,
                'preload' => true
            ],
            $mapperQuery
        );
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
                'key' => $key
            ],
            $mapperQuery
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
                'key' => $keys
            ],
            $mapperQuery
        );
    }
}
