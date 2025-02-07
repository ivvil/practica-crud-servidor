<?php
$template_loader = new \Twig\Loader\FilesystemLoader("ui");
$twig = new \Twig\Environment($template_loader, [
    // "cache" => "/uicache",
]);

$req = $_SERVER["REQUEST_URI"];

$action = "";

if (str_contains($req, "register")) {
    $action = "Register";
} else if (str_contains($req, "login")) {
    $action = "Login";
}

echo $twig->load("components/login.html.twig")->render([ 'action' => $action ]);
