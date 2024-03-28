<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Database\Record\SelectQueryInterface;
use Pyncer\Snyppet\Access\User\UserGroup;

use function Pyncer\Array\unset_keys as pyncer_array_unset_keys;

class UserMapperQuery extends AbstractRequestMapperQuery
{
    public function overrideModel(
        ModelInterface $model,
        array $data,
    ): ModelInterface
    {
        if (!$this->getOptions()) {
            return $model;
        }

        if ($this->getOptions()->hasOption('include-user-values')) {
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
        string $operator,
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

        if ($left === 'email' && is_string($right) && $operator === '=') {
            return true;
        }

        if ($left === 'phone' && is_string($right) && $operator === '=') {
            return true;
        }

        if ($left === 'username' && is_string($right) && $operator === '=') {
            return true;
        }

        if ($left === 'group' &&
            UserGroup::tryFrom($right) !== null &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        return parent::isValidFilter($left, $right, $operator);
    }

    protected function applyFilter(
        SelectQueryInterface $query,
        string $left,
        mixed $right,
        string $operator
    ): SelectQueryInterface
    {
        if ($left === 'phone') {
            $phone = preg_replace('/[^\d]/', '', $right);

            $phones = [$right, $phone];
            $phones[] = preg_replace('/[^\d\+]/', '', $right);

            if (strlen($phone) === 10) {
                $phones[] = substr($phone, 0, 3) . '-' .
                    substr($phone, 3, 3) . '-' .
                    substr($phone, 6, 4);
            }

            $phones = array_unique($phones);

            $where = $query->getWhere();

            $where->orOpen();

            foreach ($phones as $phone) {
                $where->contains('phone', $phone);
            }

            $where->orClose();

            return $query;
        }

        return parent::applyFilter($query, $left, $right, $operator);
    }

    protected function isValidOption(string $option): bool
    {
        switch ($option) {
            case 'include-user-values':
                return true;
        }

        return parent::isValidOption($option);
    }
}
