<?php
use Pyncer\Snyppet\Snyppet;
use Pyncer\Snyppet\SnyppetManager;

SnyppetManager::register(new Snyppet(
    'access',
    dirname(__DIR__),
    [
        'initialize' => ['Initialize'],
        'access' => ['User'],
    ],
));
