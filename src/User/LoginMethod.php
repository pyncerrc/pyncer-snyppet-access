<?php
namespace Pyncer\Snyppet\Access\User;

enum LoginMethod: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case USERNAME = 'username';
}
