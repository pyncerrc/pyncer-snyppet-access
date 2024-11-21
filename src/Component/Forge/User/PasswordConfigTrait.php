<?php
namespace Pyncer\Snyppet\Access\Component\Forge\User;

use Pyncer\App\Identifier as ID;
use Pyncer\Snyppet\Access\User\PasswordConfig;

trait PasswordConfigTrait
{
    private ?PasswordConfig $passwordConfig = null;
    private ?PasswordConfig $defaultPasswordConfig = null;

    public function getPasswordConfig(): ?PasswordConfig
    {
        if ($this->passwordConfig !== null) {
            return $this->passwordConfig;
        }

        if ($this->defaultPasswordConfig === null) {
            $this->defaultPasswordConfig = $this->forgePasswordConfig();
        }

        return $this->defaultPasswordConfig;
    }
    public function setPasswordConfig(?PasswordConfig $value): static
    {
        $this->passwordConfig = $value;
        return $this;
    }

    protected function forgePasswordConfig(): PasswordConfig
    {
        $config = null;

        $snyppetManager = $this->get(ID::SNYPPET);
        if ($snyppetManager->has('config')) {
            $config = $this->get(ID::config());
        }

        return new PasswordConfig($config);
    }
}
