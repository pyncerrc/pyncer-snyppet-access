<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\BoolRule;
use Pyncer\Validation\Rule\DateTimeRule;
use Pyncer\Validation\Rule\EmailRule;
use Pyncer\Validation\Rule\EnumRule;
use Pyncer\Validation\Rule\StringRule;

class UserValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'mark',
            new StringRule(
                maxLength: 250,
                allowNull: true,
            ),
        );

        $this->addRules(
            'insert_date_time',
            new DateTimeRule(),
        );

        $this->addRules(
            'update_date_time',
            new DateTimeRule(
                allowNull: true
            ),
        );

        $this->addRules(
            'group',
            new EnumRule([
                'guest', 'user', 'admin', 'super'
            ])
        );

        $this->addRules(
            'display_name',
            new StringRule(
                maxLength: 50,
                allowNull: true,
            ),
        );

        $this->addRules(
            'email',
            new StringRule(
                maxLength: 125,
                allowNull: true,
            ),
            new EmailRule()
        );

        $this->addRules(
            'phone',
            new StringRule(
                maxLength: 25,
                allowNull: true,
            ),
        );

        $this->addRules(
            'username',
            new StringRule(
                maxLength: 50,
                allowNull: true,
            ),
        );

        $this->addRules(
            'password',
            new StringRule(
                maxLength: 250,
                allowNull: true,
            ),
        );

        $this->addRules(
            'internal',
            new BoolRule()
        );

        $this->addRules(
            'enabled',
            new BoolRule()
        );

        $this->addRules(
            'deleted',
            new BoolRule()
        );
    }
}
