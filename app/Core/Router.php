<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, array $handler): void
    {
        $paramNames = [];
        $regex = preg_replace_callback('#\{(\w+)\}#', function (array $m) use (&$paramNames) {
            $paramNames[] = $m[1];
            // {id} is altijd numeriek; overige placeholders (bv. {uuid}) zijn vrije tekst-segmenten.
            return $m[1] === 'id' ? '(\d+)' : '([^/]+)';
        }, $pattern);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [$method, $regex, $handler, $paramNames];
    }

    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        if ($method === 'POST' && !Csrf::verify($this->csrfTokenFromRequest())) {
            http_response_code(419);
            echo '419 - Ongeldig of verlopen beveiligingstoken. Herlaad de pagina en probeer het opnieuw.';
            return;
        }

        foreach ($this->routes as [$routeMethod, $regex, $handler, $paramNames]) {
            if ($routeMethod !== $method) {
                continue;
            }

            if (preg_match($regex, $path, $matches)) {
                array_shift($matches);
                $params = [];
                foreach ($paramNames as $i => $name) {
                    $params[] = $name === 'id' ? (int) $matches[$i] : $matches[$i];
                }

                [$class, $action] = $handler;
                $controller = new $class();
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Pagina niet gevonden.';
    }

    private function csrfTokenFromRequest(): ?string
    {
        if (is_string($_POST['_csrf'] ?? null) && $_POST['_csrf'] !== '') {
            return $_POST['_csrf'];
        }

        $header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return is_string($header) ? $header : null;
    }
}
