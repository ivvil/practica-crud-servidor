<?php

namespace App\Crud;

session_start();

if (!isset($_SESSION["user"]) || !isset($_SESSION["pass"])){
    header("Location: /login");
    die();
}

$req = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];

$tables = DB::getInstance()->get_tablas();
$twig = Templates::getInstance();



echo $twig->load("components/tables.html.twig", [ 'tables' => $tables ]);



