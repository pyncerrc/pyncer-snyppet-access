<?php
namespace Pyncer\Snyppet\Access\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Data\Mapper\MapperAdaptor;
use Pyncer\Data\MapperQuery\FiltersQueryParam;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;
use Pyncer\Snyppet\Access\Table\Token\TokenMapper;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserMapperQuery;

class InitializeMiddleware implements MiddlewareInterface
{
    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        // Database
        if (!$handler->has(ID::DATABASE)) {
            throw new UnexpectedValueException(
                'Database connection expected.'
            );
        }

        $connection = $handler->get(ID::DATABASE);
        if (!$connection instanceof ConnectionInterface) {
            throw new UnexpectedValueException(
                'Invalid database connection.'
            );
        }

        // User mapper adaptor
        if (!$handler->has(ID::mapperAdaptor('user'))) {
            $userMapperQuery = new UserMapperQuery($connection);
            $userMapperQuery->setFilters(new FiltersQueryParam(
                'enabled eq true and deleted eq false'
            ));
            $userMapperAdaptor = new MapperAdaptor(
                new UserMapper($connection),
                $userMapperQuery
            );
            $handler->set(ID::mapperAdaptor('user'), $userMapperAdaptor);
        }

        // Token mapper adaptor
        if (!$handler->has(ID::mapperAdaptor('token'))) {
            $tokenMapperAdaptor = new MapperAdaptor(
                new TokenMapper($connection),
            );
            $handler->set(ID::mapperAdaptor('token'), $tokenMapperAdaptor);
        }

        return $handler->next($request, $response);
    }
}
