<?php

// el tablename no me lo pilla en add.php

namespace App\Crud;

use function APP\Crud\tableAdd;

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
        'path' => [
            '/table/{tablename}/new',
            '/table/{tablename}/add',
        ],
        'handler' => function ($params) {
            $tablename = $params["tablename"];
            require __DIR__ . '/views/add.php';
        }
    ],
    [
        'method' => '',
        'path' => [
            '/table/{tablename}',
            '/table/{tablename}/{row}'
        ],
        'handler' => function ($params) {
            $tablename = $params['tablename'] ?? 'default';
            $row = $params['row'] ?? 'default';
            require __DIR__ . '/views/table.php';
        },
    ],
]);

$router->resolve();
