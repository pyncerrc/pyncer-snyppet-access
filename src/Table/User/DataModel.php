<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Data\Model\AbstractModel;

class DataModel extends AbstractModel
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

    public function getKey(): string
    {
        return $this->get('key');
    }
    public function setKey(string $value): static
    {
        $this->set('key', $value);
        return $this;
    }

    public function getType(): string
    {
        return $this->get('type');
    }
    public function setType(string $value): static
    {
        $this->set('type', $value);
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
            'key' => '',
            'type' => '',
            'value' => null,
        ];
    }
}
