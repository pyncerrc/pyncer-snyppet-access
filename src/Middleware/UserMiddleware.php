<?php
namespace Pyncer\Snyppet\Access\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Access\AuthenticatorInterface;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;
use Pyncer\Snyppet\Access\Table\User\DataManager as UserDataManager;
use Pyncer\Snyppet\Access\Table\User\ValueManager as UserValueManager;

class UserMiddleware implements MiddlewareInterface
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

        // Access
        if (!$handler->has(ID::ACCESS)) {
            throw new UnexpectedValueException(
                'Access authenticator expected.'
            );
        }

        $access = $handler->get(ID::ACCESS);
        if (!$access instanceof AuthenticatorInterface) {
            throw new UnexpectedValueException('Invalid access authenticator.');
        }

        $user = $access->getUser();

        if ($user === null) {
            return $handler->next($request, $response);
        }

        ID::register(ID::user());
        ID::register(ID::user('data'));
        ID::register(ID::user('value'));

        $handler->set(ID::user(), $user);

        $dataManager = new UserDataManager($connection, $user->getId());
        $handler->set(ID::user('data'), $dataManager);

        $valueManager = new UserValueManager($connection, $user->getId());
        $valueManager->preload();
        $handler->set(ID::user('value'), $valueManager);

        return $handler->next($request, $response);
    }
}
