<?php

namespace App\Crud;

class Templates
{
    private $loader;
    private $twig;

    public function __construct(string $dir)
    {
        $this->loader = new \Twig\Loader\FilesystemLoader($dir);
        $this->twig = new \Twig\Environment($this->loader, [
            // "cache" => "/uicache",
        ]);   
    }

    public function load(string $page, array $params = []): string
    {
        return $this->twig->load($page)->render($params);
    }
}
