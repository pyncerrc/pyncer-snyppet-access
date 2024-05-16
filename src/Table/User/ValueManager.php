<?php
namespace Pyncer\Snyppet\Access\Table\User;

use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Access\Table\User\ValueMapper;
use Pyncer\Snyppet\Access\Table\User\ValueModel;
use Pyncer\Snyppet\Access\Table\User\ValueValidator;
use Pyncer\Snyppet\Utility\Data\AbstractDataManager;
use Pyncer\Snyppet\Utility\Data\PreloadInterface;
use Pyncer\Snyppet\Utility\Data\PreloadTrait;
use Pyncer\Utility\Params;

class ValueManager extends AbstractDataManager implements PreloadInterface
{
    use PreloadTrait;

    public function __construct(
        ConnectionInterface $connection,
        protected int $userId
    ) {
        parent::__construct($connection);
    }

    public function preload(): static
    {
        $mapper = new ValueMapper($this->connection);
        $result = $mapper->selectAllPreloaded($this->userId);

        foreach ($result as $valueModel) {
            $this->set($valueModel->getKey(), $valueModel->getValue());
            $this->setPreload($valueModel->getKey(), true);
        }

        return $this;
    }

    public function load(string ...$keys): static
    {
        $valueMapper = new ValueMapper($this->connection);
        $result = $valueMapper->selectAllByKeys($this->userId, $keys);

        foreach ($result as $valueModel) {
            $this->set($valueModel->getKey(), $valueModel->getValue());
            $this->setPreload($valueModel->getKey(), $valueModel->getPreload());
        }

        return $this;
    }

    public function validate(string ...$keys): array
    {
        $errors = [];

        foreach ($keys as $key) {
            $value = $this->getString($key, null);

            if ($value === null) {
                continue;
            }

            $preload = $this->getPreload($key);

            $validator = new ValueValidator($connection);
            [$data, $itemErrors] = $validator->validateData([
                'key' => $key,
                'value' => $value,
                'preload' => $preload,
            ]);

            if ($itemErrors) {
                $errors[$key] = $itemErrors;
            }
        }

        return $errors;
    }

    public function save(string ...$keys): static
    {
        $valueMapper = new ValueMapper($this->connection);

        foreach ($keys as $key) {
            $valueModel = $valueMapper->selectByKey($this->userId, $key);

            $value = $this->getString($key, null);

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

            $valueModel->setValue($value);

            $valueModel->setPreload($this->getPreload($key));

            $valueMapper->replace($valueModel);
        }

        return $this;
    }
}
