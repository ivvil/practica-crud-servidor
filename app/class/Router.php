<?php

namespace App\Crud;

class Router
{
    private $routes = [];

    public function addRoute(string | array $method, string | array $path, callable $handler)
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
        $handler = '';
        
        foreach ($this->routes as $route)
        {
            if (is_array($route['path'])) {
                foreach ($route['path'] as $this_route)
                {
                    if ($this->matchPath($url, $this_route)) {
                        $handler = $route['handler'];
                    }
                }
            }
            
            $ptrn = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $route['path']);
            $ptrn = '@^'. $ptrn . '$@';

            if ($route['method'] === '') {
                $method_matches = true;
            } else {
                $method_matches = $method === $route['method'];
            }

            if ($method_matches && preg_match($ptrn, $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return [
                    'handler' => $route['handler'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    private function matchPath($path, $url): bool
    {
        $ptrn = '@^'. preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $path) . '$@';
        return preg_match($ptrn, $url);
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
