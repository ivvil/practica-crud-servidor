<?php

namespace App\Crud;

class Router
{
    private $routes = [];

    public function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function match($method, $url)
    {
        foreach ($this->routes as $route)
        {
            $ptrn = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $route['path']);
            $ptrn = '@^'. $ptrn . '$@';

            if ($method === $route['method'] && preg_match($ptrn, $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return [
                    'handler' => $route['handler'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    public function resolve()
    {
        $req = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        $match = $this->match($method, $req);

        if ($match) {
            $handler = $match['handler'];
            $params = $match['params'];

            $handler($params);
        } else {
            http_response_code(404);
        }
    }
}
