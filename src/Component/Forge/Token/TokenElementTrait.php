<?php
namespace Pyncer\Snyppet\Access\Component\Forge\Token;

trait TokenElementTrait
{
    protected $realm;

    public function getRealm(): ?string
    {
        return $this->realm;
    }
    public function setRealm(?string $value): void
    {
        if ($value === '') {
            $value = null;
        }

        $this->realm = $value;
    }
}
