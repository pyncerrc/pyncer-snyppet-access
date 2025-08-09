<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\BoolRule;
use Pyncer\Validation\Rule\DateTimeRule;
use Pyncer\Validation\Rule\EmailRule;
use Pyncer\Validation\Rule\EnumRule;
use Pyncer\Validation\Rule\PhoneRule;
use Pyncer\Validation\Rule\RequiredRule;
use Pyncer\Validation\Rule\StringRule;

use const Pyncer\Snyppet\Access\USER_PHONE_ALLOW_E164 as PYNCER_ACCESS_USER_PHONE_ALLOW_E164;
use const Pyncer\Snyppet\Access\USER_PHONE_ALLOW_NANP as PYNCER_ACCESS_USER_PHONE_ALLOW_NANP;
use const Pyncer\Snyppet\Access\USER_PHONE_ALLOW_FORMATTING as PYNCER_ACCESS_USER_PHONE_ALLOW_FORMATTING;

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
            new RequiredRule(DateTimeRule::EMPTY),
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
            new RequiredRule(),
            new EnumRule([
                'guest', 'user', 'admin', 'super'
            ]),
        );

        $this->addRules(
            'name',
            new StringRule(
                maxLength: 50,
                allowNull: true,
            ),
        );

        $this->addRules(
            'email',
            new EmailRule(),
            new StringRule(
                maxLength: 125,
                allowNull: true,
            ),
        );

        $this->addRules(
            'phone',
            new PhoneRule(
                allowNanp: PYNCER_ACCESS_USER_PHONE_ALLOW_NANP,
                allowE164: PYNCER_ACCESS_USER_PHONE_ALLOW_E164,
                allowFormatting: PYNCER_ACCESS_USER_PHONE_ALLOW_FORMATTING,
            ),
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
            new BoolRule(),
        );

        $this->addRules(
            'enabled',
            new BoolRule(),
        );

        $this->addRules(
            'deleted',
            new BoolRule(),
        );
    }
}
