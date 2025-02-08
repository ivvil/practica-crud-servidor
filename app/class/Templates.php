<?php

namespace App\Crud;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Templates
{
    private static $instance = null;
    private $loader;
    private $twig;

    private function __construct(string $dir)
    {
        $this->loader = new FilesystemLoader($dir);
        $this->twig = new Environment($this->loader, [
            // "cache" => "/uicache", // Uncomment to enable caching
        ]);
    }

    public function load(string $page, array $params = []): string
    {
        $params["user"]["logged"] = isset($_SESSION["user"]) && isset($_SESSION["pass"]);
        return $this->twig->render($page, $params);
    }

    public static function getInstance(string $dir = ''): Templates
    {
        if (self::$instance === null) {
            if (empty($dir)) {
                throw new \RuntimeException("Template directory must be provided for the first instance.");
            }
            self::$instance = new Templates($dir);
        }

        return self::$instance;
    }
}
