<?php
namespace Pyncer\Snyppet\Access\Component\Module\Token;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractModule;
use Pyncer\Data\MapperQuery\FiltersQueryParam;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Access\Component\Forge\Token\TokenElementTrait;
use Pyncer\Snyppet\Access\Table\Token\TokenMapper;
use Pyncer\Snyppet\Access\Table\Token\TokenMapperQuery;

use const Pyncer\Snyppet\Access\DEFAULT_RELAM as PYNCER_ACCESS_DEFAULT_RELAM;
use const Pyncer\Snyppet\Access\DEFAULT_SCHEME as PYNCER_ACCESS_DEFAULT_SCHEME;

class DeleteTokenItemModule extends AbstractModule
{
    use TokenElementTrait;

    protected ?RoutingPathInterface $idRoutingPath = null;

    public function getIdRoutingPath(): ?RoutingPathInterface
    {
        return $this->idRoutingPath;
    }
    public function setIdRoutingPath(?RoutingPathInterface $value): static
    {
        $this->idRoutingPath = $value;
        return $this;
    }

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        $connection = $this->get(ID::DATABASE);

        $tokenMapper = new TokenMapper($connection);
        $tokenMapperQuery = $this->forgeMapperQuery();
        $tokenModel = null;

        $idRoutingPath = $this->getIdRoutingPath()?->getRouteDirPath() ?? '@id64';
        if ($idRoutingPath === '@id64') {
            $id64 = $this->queryParams->getString(
                $this->getIdRoutingPath()?->getQueryName() ?? 'id64',
                null
            );

            if ($id64 !== null) {
                $tokenModel = $tokenMapper->selectByColumns(
                    ['token' => $id64],
                    $tokenMapperQuery
                );
            }
        } elseif ($idRoutingPath === '@id') {
            $id = $this->queryParams->getInt(
                $this->getIdRoutingPath()?->getQueryName() ?? 'id',
                null
            );

            if ($id !== null) {
                $tokenModel = $tokenMapper->selectById($id, $tokenMapperQuery);
            }
        } else {
            throw new UnexpectedValueException(
                'Id routing path is not supported. (' . $idRoutingPath . ')'
            );
        }

        if (!$tokenModel) {
            return new Response(
                Status::CLIENT_ERROR_404_NOT_FOUND
            );
        }

        $tokenMapper->delete($tokenModel);

        return new Response(
            Status::SUCCESS_204_NO_CONTENT
        );
    }

    /**
    * @return \Pyncer\Data\MapperQuery\MapperQueryInterface
    */
    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        $tokenMapperQuery = new TokenMapperQuery($connection);

        // Filters
        $scheme = $this->getScheme() ?? PYNCER_ACCESS_DEFAULT_SCHEME;
        $filters = 'scheme eq \'' . $scheme . '\' and ';

        $realm = $this->getRealm() ?? PYNCER_ACCESS_DEFAULT_REALM;

        $filters .= 'realm eq \'' . $realm . '\'';

        $filters = new FiltersQueryParam($filters);
        $tokenMapperQuery->setFilters($filters);

        return $tokenMapperQuery;
    }
}
