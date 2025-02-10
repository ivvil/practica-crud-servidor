<?php

namespace App\Crud;

class Router
{
    private $routes = [];

    public function addRoute($method, $path, callable $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function addRoutes(array $routes)
    {
        $this->routes = array_merge($this->routes, $routes);
    }

    public function match($method, $url)
    {
        foreach ($this->routes as $route) {
            $methods = is_array($route['method']) ? $route['method'] : [$route['method']];
            $paths = is_array($route['path']) ? $route['path'] : [$route['path']];

            foreach ($paths as $path) {
                $pattern = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $path);
                $pattern = '@^' . $pattern . '$@';

                if (preg_match($pattern, $url, $matches)) {
                    if (in_array('', $methods) || in_array($method, $methods)) {
                        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                        return [
                            'handler' => $route['handler'],
                            'params' => $params,
                        ];
                    }
                }
            }
        }

        return null;
    }

    public function resolve()
    {
        $req = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $match = $this->match($method, $req);

        if ($match) {
            $handler = $match['handler'];
            $params = $match['params'];

            $handler($params);
        } else {
            http_response_code(404);
            echo "Page not found.";
        }
    }
}
