<?php
namespace Core;

class Router {
    private array $routes = [];

    public function get($uri, $action, $middleware = []) {
        $this->routes['GET'][$uri] = compact('action','middleware');
    }

    public function post($uri, $action, $middleware = []) {
        $this->routes['POST'][$uri] = compact('action','middleware');
    }

    public function dispatch() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            $route = $this->routes[$method][$uri] ?? null;
            if (!$route) {
                ErrorHandler::render404();
                return;
            }

            foreach ($route['middleware'] as $mw) {
                $class = "\\App\\Middlewares\\{$mw}";
                (new $class)->handle();
            }

            [$class, $method] = $route['action'];
            echo call_user_func([new $class, $method]);
        } catch (\Throwable $e) {
            ErrorHandler::handleException($e);
        }
    }
}