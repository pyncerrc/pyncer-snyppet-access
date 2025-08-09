<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Snyppet\Access\User\Table\User\UserMapper;
use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\IdRule;
use Pyncer\Validation\Rule\IntRule;
use Pyncer\Validation\Rule\RequiredRule;
use Pyncer\Validation\Rule\StringRule;

class DataValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'user_id',
            new RequiredRule(IntRule::EMPTY),
            new IntRule(
                minValue: 0,
            ),
            new IdRule(
                mapper: new UserMapper($this->getConnection()),
            ),
        );

        $this->addRules(
            'key',
            new RequiredRule(),
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'type',
            new RequiredRule(),
            new StringRule(
                maxLength: 125,
            ),
        );

        $this->addRules(
            'value',
            new RequiredRule(),
            new StringRule(
                maxLength: 4000000,
            ),
        );
    }
}
