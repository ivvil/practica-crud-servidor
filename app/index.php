<?php

namespace App\Crud;

require_once "vendor/autoload.php";

$tmplt = Templates::getInstance(__DIR__ . '/ui');

$router = new Router();

$router->addRoutes([
    [
        'method' => '',
        'path' => [
            '/',
            '/login',
            '/register',
        ],
        'handler' => function () {
            require __DIR__ . '/views/login.php';
        },
    ],
    [
        'method' => '',
        'path' => '/tables',
        'handler' => function () {
            require __DIR__ . '/views/tables.php';
        },
    ],
    [
        'method' => '',
        'path' => '/table/{tablename}',
        'handler' => function ($params) {
            $tablename = $params['tablename'] ?? 'default';
            require __DIR__ . '/views/table.php';
        },
    ],
]);

$router->resolve();

