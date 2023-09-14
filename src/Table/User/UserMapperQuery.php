<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;
use Pyncer\Snyppet\Access\User\UserGroup;

class UserMapperQuery extends AbstractRequestMapperQuery
{
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
}
