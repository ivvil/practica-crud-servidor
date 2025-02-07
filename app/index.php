<?php

require_once "vendor/autoload.php";


$req = $_SERVER["REQUEST_URI"];
$view_dir = "/views/";

switch ($req) {
	case "":
	case "/":
	case "/login":
	case "/register":		
		require __DIR__ . $view_dir . "login.php";
		break;
	default:
		http_response_code(404);
		break;
}
