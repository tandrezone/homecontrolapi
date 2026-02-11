<?php

declare(strict_types=1);

namespace HomeControl\Http;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): Response
    {
        // Remove query string
        $uri = strtok($uri, '?');

        // Try exact match first
        if (isset($this->routes[$method][$uri])) {
            return call_user_func($this->routes[$method][$uri]);
        }

        // Try pattern matching for routes with parameters
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $handler) {
                $pattern = $this->convertRouteToRegex($route);
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // Remove full match
                    return call_user_func_array($handler, $matches);
                }
            }
        }

        return new Response(['error' => 'Not Found'], 404);
    }

    private function convertRouteToRegex(string $route): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
}
