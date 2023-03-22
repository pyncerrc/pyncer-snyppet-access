<?php
namespace Pyncer\Snyppet\Access\Table\Token;

use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Validation\Rule\DateTimeRule;
use Pyncer\Validation\Rule\IdRule;
use Pyncer\Validation\Rule\StringRule;

final class TokenValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRule(
            'user_id',
            new IdRule(
                mapper: new UserMapper($this->getConnection()),
                allowNull: true,
            ),
        );

        $this->addRule(
            'scheme',
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRule(
            'realm',
            new StringRule(
                maxLength: 250,
            ),
        );

        $this->addRule(
            'token',
            new StringRule(
                maxLength: 96,
            ),
        );

        $this->addRule(
            'expiration_date_time',
            new DateTimeRule(
                allowNull: true,
            ),
        );
    }
}
