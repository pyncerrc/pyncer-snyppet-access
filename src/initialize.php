<?php
namespace Pyncer\Snyppet\Access;

use Pyncer\Validation\Rule\PasswordRule;
use Pyncer\Snyppet\Access\User\LoginMethod;

defined('Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS') or define('Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS', false);
defined('Pyncer\Snyppet\Access\DEFAULT_SCHEME') or define('Pyncer\Snyppet\Access\DEFAULT_SCHEME', 'Bearer');
defined('Pyncer\Snyppet\Access\DEFAULT_REALM') or define('Pyncer\Snyppet\Access\DEFAULT_REALM', 'app');
defined('Pyncer\Snyppet\Access\LOGIN_METHOD') or define('Pyncer\Snyppet\Access\LOGIN_METHOD', LoginMethod::EMAIL);
defined('Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION') or define('Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION', 172800);
defined('Pyncer\Snyppet\Access\USER_GUEST_ID') or define('Pyncer\Snyppet\Access\USER_GUEST_ID', 1);
defined('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_E164') or define('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_E164', true);
defined('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_NANP') or define('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_NANP', false);
defined('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_FORMATTING') or define('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_FORMATTING', false);

defined('Pyncer\Snyppet\Access\PASSWORD_CONFIRM_NEW') or define('Pyncer\Snyppet\Access\PASSWORD_CONFIRM_NEW', false);
defined('Pyncer\Snyppet\Access\PASSWORD_CONFIRM_OLD') or define('Pyncer\Snyppet\Access\PASSWORD_CONFIRM_OLD', false);
defined('Pyncer\Snyppet\Access\PASSWORD_MIN_LENGTH') or define('Pyncer\Snyppet\Access\PASSWORD_MIN_LENGTH', null);
defined('Pyncer\Snyppet\Access\PASSWORD_MAX_LENGTH') or define('Pyncer\Snyppet\Access\PASSWORD_MAX_LENGTH', null);
defined('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_NUMERIC_CHARACTERS') or define('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_NUMERIC_CHARACTERS', false);
defined('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_ALPHA_CHARACTERS') or define('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_ALPHA_CHARACTERS', false);
defined('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS') or define('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS', false);
defined('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS') or define('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS', false);
defined('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_SPECIAL_CHARACTERS') or define('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_SPECIAL_CHARACTERS', false);
defined('Pyncer\Snyppet\Access\PASSWORD_SPECIAL_CHARACTERS') or define('Pyncer\Snyppet\Access\PASSWORD_SPECIAL_CHARACTERS', PasswordRule::SPECIAL_CHARACTERS);
defined('Pyncer\Snyppet\Access\PASSWORD_ALLOW_WHITESPACE') or define('Pyncer\Snyppet\Access\PASSWORD_ALLOW_WHITESPACE', false);
