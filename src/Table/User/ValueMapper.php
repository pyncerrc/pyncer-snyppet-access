<?php
namespace Pyncer\Snyppet\Access\User\Table\User;

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
}
