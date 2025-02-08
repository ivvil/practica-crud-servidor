<?php
$template_loader = new \Twig\Loader\FilesystemLoader("ui");
$twig = new \Twig\Environment($template_loader, [
    // "cache" => "/uicache",
]);

$req = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];

$action = "";

if (str_contains($req, "register")) {
    $action = "Register";
} else if (str_contains($req, "login")) {
    $action = "Login";
}

if ($method == 'POST') {
    
}

echo $twig->load("components/login.html.twig")->render([ 'action' => $action ]);
