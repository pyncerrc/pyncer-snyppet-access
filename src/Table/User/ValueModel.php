<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\Model\AbstractModel;

class ValueModel extends AbstractModel
{
    public function getUserId(): int
    {
        return $this->get('user_id');
    }
    public function setUserId(int $value): static
    {
        $this->set('user_id', $value);
        return $this;
    }

    public function getGroup(): ?string
    {
        return $this->get('group');
    }
    public function setGroup(?string $value): static
    {
        $this->set('group', $this->nullify($value));
        return $this;
    }

    public function getKey(): string
    {
        return $this->get('key');
    }
    public function setKey(string $value): static
    {
        $this->set('key', $value);
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->get('value');
    }
    public function setValue(?string $value): static
    {
        $this->set('value', $this->nullify($value));
        return $this;
    }

    public static function getDefaultData(): array
    {
        return [
            'id' => 0,
            'user_id' => 0,
            'group' => null,
            'key' => '',
            'value' => null,
        ];
    }
}
