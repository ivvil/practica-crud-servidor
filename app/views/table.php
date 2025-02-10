<?php

namespace App\Crud;

session_start();

if (!isset($_SESSION["user"]) || !isset($_SESSION["pass"])){
    header("Location: /login");
    die();
}

$req = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// $tablename = substr($req, strpos($req, "/table"));

var_dump($tablename);
