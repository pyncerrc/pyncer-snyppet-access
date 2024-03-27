<?php
namespace Pyncer\Snyppet\Access\Table\Token;

use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Access\Table\Token\TokenMapperQuery;
use Pyncer\Snyppet\Access\Table\Token\TokenModel;

class TokenMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'token';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new TokenModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof TokenModel);
    }

    public function isValidMapperQuery(MapperQueryInterface $mapperQuery): bool
    {
        return ($mapperQuery instanceof TokenMapperQuery);
    }

    public function selectByToken(
        string $scheme,
        string $realm,
        string $token,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        return $this->selectByColumns(
            [
                'scheme' => $scheme,
                'realm' => $realm,
                'token' => $token
            ],
            $mapperQuery
        );
    }
}
