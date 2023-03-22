<?php
namespace Pyncer\Snyppet\Access\Table\Token;

use DateTime;
use DateTimeInterface;
use Pyncer\Data\Model\AbstractModel;

use function Pyncer\date_time as pyncer_date_time;

use const Pyncer\DATE_TIME_FORMAT as PYNCER_DATE_TIME_FORMAT;

class TokenModel extends AbstractModel
{
    public function getUserId(): ?int
    {
        return $this->get('user_id');
    }
    public function setUserId(?int $value): static
    {
        $this->set('user_id', $this->nullify($value));
        return $this;
    }

    public function getScheme(): string
    {
        return $this->get('scheme');
    }
    public function setScheme(string $value): static
    {
        $this->set('scheme', $value);
        return $this;
    }

    public function getRealm(): string
    {
        return $this->get('realm');
    }
    public function setRealm(string $value): static
    {
        $this->set('realm', $value);
        return $this;
    }

    public function getToken(): string
    {
        return $this->get('token');
    }
    public function setToken(string $value): static
    {
        $this->set('token', $value);
        return $this;
    }

    public function getExpirationDateTime(): ?DateTime
    {
        $value = $this->get('expiration_date_time');
        return pyncer_date_time($value);
    }
    public function setExpirationDateTime(null|string|DateTimeInterface $value): static
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(PYNCER_DATE_TIME_FORMAT);
        }
        $this->set('expiration_date_time', $this->nullify($value));
        return $this;
    }

    public static function getDefaultData(): array
    {
        return [
            'id' => 0,
            'user_id' => null,
            'scheme' => '',
            'realm' => '',
            'token' => '',
            'expiration_date_time' => null,
        ];
    }
}
