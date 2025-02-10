<?php

namespace App\Crud;

session_start();

if (!isset($_SESSION["user"]) || !isset($_SESSION["pass"])){
    header("Location: /login");
    die();
}

$req = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$twig = Templates::getInstance();

if ($method == 'DELETE') {
    DB::getInstance()->borrar_fila($tablename, $row);
} else {
    echo $twig->load('layouts/table.html.twig', [ 'table' => DB::getInstance()->get_filas($tablename), 'tablename' => $tablename ]);       
}
