<?php
namespace Pyncer\Snyppet\Access\Table\Token;

use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Database\Record\SelectQueryInterface;
use Pyncer\Snyppet\Access\Table\User\UserModel;

use function Pyncer\Array\filter_prefixed_keys as array_filter_prefixed_keys;

class TokenMapperQuery extends AbstractRequestMapperQuery
{
    public function overrideModel(
        ModelInterface $model,
        array $data
    ): ModelInterface
    {
        if (!$this->getOptions()) {
            return $model;
        }

        if ($this->getOptions()->hasOption('include-user')) {
            $sideData = array_filter_prefixed_keys($data, 'user__', true);
            $sideModel = new UserModel($sideData);
            $model->getSideModels()->set('user', $sideModel);
        }

        return $model;
    }

    protected function isValidFilter(
        string $left,
        mixed $right,
        string $operator
    ): bool
    {
        if ($left === 'scheme' && is_string($right) && $operator === '=') {
            return true;
        }

        if ($left === 'realm' && is_string($right) && $operator === '=') {
            return true;
        }

        return parent::isValidFilter;
    }

    protected function isValidOption(string $option): bool
    {
        switch ($option) {
            case 'include-user':
                return true;
        }

        return parent::isValidOption($option);
    }
    protected function applyOption(
        SelectQueryInterface $query,
        string $option
    ): SelectQueryInterface
    {
        if ($option === 'include-user') {
            if (!$query->hasJoined('user')) {
                $query->leftJoin('user', 'id', 'user_id');
            }

            $query->columns(['user', '*', 'user__']);
            return $query;
        }

        return parent::applyOption($query, $option);
    }
}
