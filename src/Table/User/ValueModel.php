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

    public function getPreload(): bool
    {
        return $this->get('preload');
    }
    public function setPreload(bool $value): static
    {
        $this->set('preload', $value);
        return $this;
    }

    public static function getDefaultData(): array
    {
        return [
            'id' => 0,
            'user_id' => 0,
            'key' => '',
            'value' => null,
            'preload' => false,
        ];
    }
}
