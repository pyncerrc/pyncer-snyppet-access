<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;
use Pyncer\Snyppet\Access\User\UserGroup;

use function Pyncer\Array\unset_keys as pyncer_array_unset_keys;

class UserMapperQuery extends AbstractRequestMapperQuery
{
    public function overrideModel(
        ModelInterface $model,
        array $data
    ): ModelInterface
    {
        if (!$this->getOptions()) {
            return $model;
        }

        if ($this->getOptions()->hasOption('include-values')) {
            $result = $this->getConnection()->select('user__value')
                ->columns('key', 'value')
                ->where(['user_id' => $model->getId()])
                ->result();

            $extraData = [];
            foreach ($result as $row) {
                $extraData[$row['key']] = $row['value'];
            }

            $extraData = pyncer_array_unset_keys($extraData, $model->getKeys());
            $model->setExtraData($extraData);
        }

        return $model;
    }

    protected function isValidFilter(
        string $left,
        mixed $right,
        string $operator
    ): bool
    {
        if ($left === 'internal' && is_bool($right) && $operator === '=') {
            return true;
        }

        if ($left === 'enabled' && is_bool($right) && $operator === '=') {
            return true;
        }

        if ($left === 'deleted' && is_bool($right) && $operator === '=') {
            return true;
        }

        if ($left === 'group' &&
            UserGroup::tryFrom($right) !== null &&
            $operator === '='
        ) {
            return true;
        }

        return parent::isValidFilter($left, $right, $operator);
    }

    protected function isValidOption(string $option): bool
    {
        switch ($option) {
            case 'include-values':
                return true;
        }

        return parent::isValidOption($option);
    }
}
