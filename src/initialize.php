<?php
namespace Pyncer\Snyppet\Access;

use Pyncer\Initializer;
use Pyncer\Snyppet\Access\User\LoginMethod;
use Pyncer\Validation\Rule\PasswordRule;

Initializer::define('Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS', false);
Initializer::define('Pyncer\Snyppet\Access\DEFAULT_SCHEME', 'Bearer');
Initializer::define('Pyncer\Snyppet\Access\DEFAULT_REALM', 'app');
Initializer::define('Pyncer\Snyppet\Access\LOGIN_METHOD', LoginMethod::EMAIL);
Initializer::define('Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION', 172800);
Initializer::define('Pyncer\Snyppet\Access\VALIDATE_LOGIN_NOT_FOUND', false);
Initializer::define('Pyncer\Snyppet\Access\USER_GUEST_ID', 1);

Initializer::defineFrom('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_E164', 'Pyncer\Validation\PHONE_ALLOW_E164', true);
Initializer::defineFrom('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_NANP', 'Pyncer\Validation\PHONE_ALLOW_NANP', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\USER_PHONE_ALLOW_FORMATTING', 'Pyncer\Validation\PHONE_ALLOW_FORMATTING', false);

Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_CONFIRM_NEW', 'Pyncer\Validation\PASSWORD_CONFIRM_NEW', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_CONFIRM_OLD', 'Pyncer\Validation\PASSWORD_CONFIRM_OLD', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_MIN_LENGTH', 'Pyncer\Validation\PASSWORD_MIN_LENGTH', null);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_MAX_LENGTH', 'Pyncer\Validation\PASSWORD_MAX_LENGTH', null);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_NUMERIC_CHARACTERS', 'Pyncer\Validation\PASSWORD_REQUIRE_NUMERIC_CHARACTERS', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_ALPHA_CHARACTERS', 'Pyncer\Validation\PASSWORD_REQUIRE_ALPHA_CHARACTERS', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS', 'Pyncer\Validation\PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS', 'Pyncer\Validation\PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_REQUIRE_SPECIAL_CHARACTERS', 'Pyncer\Validation\PASSWORD_REQUIRE_SPECIAL_CHARACTERS', false);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_SPECIAL_CHARACTERS', 'Pyncer\Validation\PASSWORD_SPECIAL_CHARACTERS', PasswordRule::SPECIAL_CHARACTERS);
Initializer::defineFrom('Pyncer\Snyppet\Access\PASSWORD_ALLOW_WHITESPACE', 'Pyncer\Validation\PASSWORD_ALLOW_WHITESPACE', false);
