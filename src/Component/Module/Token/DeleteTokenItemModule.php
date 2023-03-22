<?php
namespace Pyncer\Snyppet\Access\Component\Module\Token;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Access\Table\Token\TokenMapper;

use const Pyncer\Snyppet\Access\DEFAULT_RELAM as PYNCER_ACCESS_DEFAULT_RELAM;

class DeleteTokenItemModule extends AbstractModule
{
    use TokenElementTrait;

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        $connection = $this->get(ID::DATABASE);

        $tokenMapper = new TokenMapper($connection);
        $tokenMapperQuery = $this->forgeMapperQuery();
        $tokenModel = null;

        $id = $this->queryParams->getInt('id', null);
        if ($id !== null) {
            $tokenModel = $tokenMapper->selectById($id, $tokenMapperQuery);
        } else {
            $id64 = $this->queryParams->getStr('id64', null);
            if ($id64 !== null) {
                $tokenModel = $tokenMapper->selectByToken(
                    'Bearer',
                    $this->getRealm() ?? PYNCER_ACCESS_DEFAULT_REALM,
                    $id64,
                    $tokenMapperQuery
                );
            }
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
        $tokenMapperQuery = new TokenMapperQuery();

        // Filters
        $filters = 'scheme eq \'Bearer\' and ';

        $realm = $this->getRealm() ?? PYNCER_ACCESS_DEFAULT_REALM

        $filters .= 'realm eq \'' . $realm . '\'';

        $filters = new FiltersQueryParam($filters);
        $tokenMapperQuery->setFilter($filters);

        return $tokenMapperQuery;
    }
}
