<?php
namespace Pyncer\Snyppet\Access\User;

use Pyncer\Snyppet\Access\Table\User\ValueMapper;
use Pyncer\Snyppet\Access\Table\User\ValueModel;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Database\ConnectionTrait;
use Pyncer\Utility\Params;

use function Pyncer\Array\data_explode as pyncer_data_explode;
use function Pyncer\Array\data_implode as pyncer_data_implode;

class UserValueManager extends Params
{
    use ConnectionTrait;

    private array $preload = [];

    public function __construct(
        ConnectionInterface $connection,
        protected int $userId
    ) {
        $this->setConnection($connection);

    }

    public function getArray(string $key, $empty = []): ?Array
    {
        $value = $this->get($key);
        if ($value !== null) {
            $value = pyncer_data_explode(',', $value);
        }

        if ($value === null || $value === []) {
            $value = $empty;
        }

        return $value;
    }

    public function setArray(string $key, ?iterable $value): static
    {
        if ($value === null) {
            $this->set($key, null);
            return $this;
        }

        $this->set($key, pyncer_data_implode(',', [...$value]));
        return $this;
    }

    public function getPreload(string $key): bool
    {
        return $this->preload[$key] ?? false;
    }

    public function setPreload(string $key, bool $value): static
    {
        $this->preload[$key] = $value;
        return $this;
    }

    public function preload(): static
    {
        $mapper = new ValueMapper($this->getConnection());
        $result = $mapper->selectAllPreloaded($this->userId);

        foreach ($result as $valueModel) {
            $this->set($valueModel->getKey(), $valueModel->getValue());
            $this->preload[$valueModel->getKey()] = true;
        }

        return $this;
    }

    public function load(string ...$keys): static
    {
        $valueMapper = new ValueMapper($this->getConnection());
        $result = $valueMapper->selectAllByKeys($this->userId, $keys);

        foreach ($result as $valueModel) {
            $this->set($valueModel->getKey(), $valueModel->getValue());
        }

        return $this;
    }
    public function save(string ...$keys): static
    {
        $valueMapper = new ValueMapper($this->getConnection());

        foreach ($keys as $key) {
            $valueModel = $valueMapper->selectByKey($this->userId, $key);

            $value = $this->get($key);

            if ($value === null) {
                if ($valueModel) {
                    $valueMapper->delete($valueModel);
                }

                continue;
            }

            if (!$valueModel) {
                $valueModel = new ValueModel();
                $valueModel->setUserId($this->userId);
                $valueModel->setKey($key);
            }

            $value = match ($value) {
                true => '1',
                false => '0',
                default => strval($value),
            };

            $valueModel->setValue($value);

            $valueModel->setPreload($this->preload[$key] ?? false);

            $valueMapper->replace($valueModel);
        }

        return $this;
    }
}
