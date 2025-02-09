<?php

namespace App\Crud;

require_once "vendor/autoload.php";

$tmplt = Templates::getInstance(__DIR__ . '/ui');

$req = $_SERVER["REQUEST_URI"];
$view_dir = "/views/";

$router = new Router();

$router->addRoutes([
    [
        'method' => 'POST',
        'path' => '/',
        'handler' => function () {
            require __DIR__ . $view_dir . "login.php";
        },
    ],
    [
        'method' => 'POST',
        'path' => '/',
        'handler' => function () {
            require __DIR__ . $view_dir . "login.php";
        },
    ]
]);

switch ($req) {
	case "":
	case "/":
	case "/login":
	case "/register":		
		require __DIR__ . $view_dir . "login.php";
		break;
	case "/tables":
		require __DIR__ . $view_dir . "tables.php";
		break;
	default:
		http_response_code(404);
		break;
}
