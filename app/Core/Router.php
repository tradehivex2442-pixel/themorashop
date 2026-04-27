<?php
// ============================================================
// THEMORA SHOP — Router
// ============================================================

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $globalMiddlewares = [
        \App\Middleware\CorsMiddleware::class,
    ];

    public function get(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares): void
    {
        // Convert :param to regex capture group
        $pattern = preg_replace('#:([a-zA-Z_]+)#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'      => strtoupper($method),
            'path'        => $path,
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(Request $request): void
    {
        // Run global middlewares (CORS etc)
        foreach ($this->globalMiddlewares as $mw) {
            (new $mw())->handle($request);
        }

        $method = $request->method();
        $uri    = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                // Run middlewares
                foreach ($route['middlewares'] as $mw) {
                    $instance = new $mw();
                    $instance->handle($request);
                }

                // Dispatch handler
                if (is_callable($route['handler'])) {
                    call_user_func($route['handler'], $request);
                    return;
                }

                [$class, $method] = $route['handler'];
                $controller = new $class();
                $controller->$method($request);
                return;
            }
        }

        // 404
        http_response_code(404);
        $view = new View();
        $view->render('errors/404');
    }
}
