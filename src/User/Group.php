<?php
namespace Pyncer\Snyppet\Access\User;

enum Group: string
{
    case GUEST = 'guest';
    case SUPER = 'super';
    case ADMIN = 'admin';
    case USER = 'user';
}
