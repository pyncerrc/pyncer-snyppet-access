<?php
namespace Pyncer\Snyppet\Access\Component\Forge\Token;

use Pyncer\Routing\Path\RoutingPathInterface;

trait TokenElementTrait
{
    protected ?string $scheme = null;
    protected ?string $realm = null;

    public function getScheme(): ?string
    {
        return $this->scheme;
    }
    public function setScheme(?string $value): static
    {
        if ($value === '') {
            $value = null;
        }

        $this->scheme = $value;
        return $this;
    }

    public function getRealm(): ?string
    {
        return $this->realm;
    }
    public function setRealm(?string $value): static
    {
        if ($value === '') {
            $value = null;
        }

        $this->realm = $value;
        return $this;
    }
}
