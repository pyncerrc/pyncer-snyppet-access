<?php
namespace Pyncer\Snyppet\Access;

use Pyncer\Snyppet\Access\User\LoginMethod;

defined('Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS') or define('Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS', false);
defined('Pyncer\Snyppet\Access\DEFAULT_SCHEME') or define('Pyncer\Snyppet\Access\DEFAULT_SCHEME', 'Bearer');
defined('Pyncer\Snyppet\Access\DEFAULT_REALM') or define('Pyncer\Snyppet\Access\DEFAULT_REALM', 'app');
defined('Pyncer\Snyppet\Access\LOGIN_METHOD') or define('Pyncer\Snyppet\Access\LOGIN_METHOD', LoginMethod::EMAIL);
defined('Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION') or define('Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION', 172800);
defined('Pyncer\Snyppet\Access\USER_GUEST_ID') or define('Pyncer\Snyppet\Access\USER_GUEST_ID', 1);
