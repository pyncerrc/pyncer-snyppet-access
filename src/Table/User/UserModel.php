<?php
namespace Pyncer\Snyppet\Access\Table\User;

use DateTime;
use DateTimeInterface;
use Pyncer\Data\Model\AbstractModel;
use Pyncer\Snyppet\Access\User\UserGroup;

use function Pyncer\date_time as pyncer_date_time;

use const Pyncer\DATE_TIME_FORMAT as PYNCER_DATE_TIME_FORMAT;

class UserModel extends AbstractModel
{
    public function getMark(): ?string
    {
        return $this->get('mark');
    }
    public function setMark(?string $value): static
    {
        $this->set('mark', $this->nullify($value));
        return $this;
    }

    public function getInsertDateTime(): DateTime
    {
        $value = $this->get('insert_date_time');
        return pyncer_date_time($value);
    }
    public function setInsertDateTime(string|DateTimeInterface $value): static
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(PYNCER_DATE_TIME_FORMAT);
        }
        $this->set('insert_date_time', $value);
        return $this;
    }

    public function getUpdateDateTime(): ?DateTime
    {
        $value = $this->get('update_date_time');
        return pyncer_date_time($value);
    }
    public function setUpdateDateTime(null|string|DateTimeInterface $value): static
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(PYNCER_DATE_TIME_FORMAT);
        }
        $this->set('update_date_time', $this->nullify($value));
        return $this;
    }

    public function getGroup(): UserGroup
    {
        $value = $this->get('group');
        return UserGroup::from($value);
    }
    public function setGroup(string|UserGroup $value): static
    {
        if ($value instanceof UserGroup) {
            $value = $value->value;
        }

        $this->set('group', $value);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->get('name');
    }
    public function setName(?string $value): static
    {
        $this->set('name', $this->nullify($value));
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->get('email');
    }
    public function setEmail(?string $value): static
    {
        $this->set('email', $this->nullify($value));
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->get('phone');
    }
    public function setPhone(?string $value): static
    {
        $this->set('phone', $this->nullify($value));
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->get('username');
    }
    public function setUsername(?string $value): static
    {
        $this->set('username', $this->nullify($value));
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->get('password');
    }
    public function setPassword(?string $value): static
    {
        $this->set('password', $this->nullify($value));
        return $this;
    }

    public function getInternal(): bool
    {
        return $this->get('internal');
    }
    public function setInternal(bool $value): static
    {
        $this->set('internal', $value);
        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->get('enabled');
    }
    public function setEnabled(bool $value): static
    {
        $this->set('enabled', $value);
        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->get('deleted');
    }
    public function setDeleted(bool $value): static
    {
        $this->set('deleted', $value);
        return $this;
    }

    public static function getDefaultData(): array
    {
        $date = pyncer_date_time()->format(PYNCER_DATE_TIME_FORMAT);

        return [
            'id' => 0,
            'mark' => null,
            'insert_date_time' => $date,
            'update_date_time' => null,
            'group' => 'user',
            'name' => null,
            'email' => null,
            'phone' => null,
            'username' => null,
            'password' => null,
            'internal' => true,
            'enabled' => true,
            'deleted' => false,
        ];
    }
}
