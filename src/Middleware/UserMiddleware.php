<?php
namespace Pyncer\Snyppet\Access\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;
use Pyncer\Snyppet\Access\User\ValueManager;

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

        ID::register('user');

        $handler->set(ID::user(), $user);

        $valueManager = new ValueManager($connection, $user->getId());
        $valueManager->preload();

        $handler->set(ID::user('value'), $valueManager);

        return $handler->next($request, $response);
    }
}
