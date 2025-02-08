<?php

namespace App\Crud;

session_start();

$req = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];

$action = "";
$login_status = false;

$error = [];

if (str_contains($req, "register")) {
    $action = "Register";
} else if (str_contains($req, "login")) {
    $action = "Login";
}

function login(string $user, string $pass, DB $db): bool
{
    if (!$db->existe_usuario($user)) {
        $GLOBALS['error']['login'] = "El usuario no son correctos";
        return false;
    }

    if (!$db->comprobar_contrasegna($user, $pass)){
        $GLOBALS['error']['login'] = "El usuario no son correctos";
        return false;
    }

    $_SESSION['user'] = serialize($user);
    $_SESSION['pass'] = serialize($pass);
    header("Location: /tables");
    die();
}

function register(string $user, string $pass, string $re_pass, DB $db): bool
{
    if ($pass !== $re_pass) {
        $GLOBALS['error']['passwd'] = "Las contraseñas no coinciden";
        return false;
    }

    if ($db->existe_usuario($user)) {
        $GLOBALS['error']['user'] = "El usuario ya existe";
        return false;
    }

    $db->registrar_usuario($user, $pass);
    $_SESSION['user'] = serialize($user);
    $_SESSION['pass'] = serialize($pass);

    return true;
}

function logout()
{
    unset($_SESSION['user']);
    unset($_SESSION['pass']);
}


if ($method == 'POST') {
    $submit = $_POST["submit"];

    $db = DB::getInstance();
    
    switch ($submit) {
        case "Logout":
            logout();
            break;
            case "Register":
            case "Login":
                $user = $_POST["user"];
                $pass = $_POST["password"];
                $re_pass = $_POST["re-password"] ?? "";
                
            $login_status = match ($submit) {
                "Login" => login($user, $pass, $db),
                "Register" => register($user, $pass, $re_pass, $db)
            };
            break;
    };
}

if (isset($_SESSION["user"]) && isset($_SESSION["pass"])) {

        header("Location: /tables");
        die();
    
}

$twig = Templates::getInstance();

echo $twig->load("components/login.html.twig", [
    'action' => $action,
    'login_status' => $login_status,
    'error' => $error
]);
