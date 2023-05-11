<?php
namespace Pyncer\Snyppet\Access\Table\Token;

use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Validation\Rule\DateTimeRule;
use Pyncer\Validation\Rule\IdRule;
use Pyncer\Validation\Rule\RequiredRule;
use Pyncer\Validation\Rule\StringRule;

class TokenValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'user_id',
            new IdRule(
                mapper: new UserMapper($this->getConnection()),
                allowNull: true,
            ),
        );

        $this->addRules(
            'scheme',
            new RequiredRule(),
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'realm',
            new RequiredRule(),
            new StringRule(
                maxLength: 250,
            ),
        );

        $this->addRules(
            'token',
            new RequiredRule(),
            new StringRule(
                maxLength: 96,
            ),
        );

        $this->addRules(
            'expiration_date_time',
            new DateTimeRule(
                allowNull: true,
            ),
        );
    }
}
