<?php

namespace App\Shared\Core;

class Router
{
    private static array $routes = [];
    private static ?Container $container = null;

    public static function add(string $method, string $path, array $handler, array $middlewares = []): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    public static function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach (self::$routes as $route) {
            // Converte a rota para um padrão regex: /users/{id} -> /users/(?<id>\d+)
            $pattern = preg_replace('/\{(\w+)\}/', '(?<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestPath, $matches)) {
                $authData = null;
                if (!empty($route['middlewares'])) {
                    foreach ($route['middlewares'] as $middleware) {
                        // Por enquanto, lidamos apenas com o AuthMiddleware de forma específica.
                        // Uma implementação mais robusta poderia usar um pipeline de middlewares.
                        if ($middleware === \App\Shared\Middleware\AuthMiddleware::class) {
                            $authData = $middleware::handle();
                        }
                    }
                }

                [$controllerClass, $method] = $route['handler'];

                $controller = self::$container->resolve($controllerClass, [
                    'authUser' => $authData
                ]);

                // Filtra para pegar apenas os parâmetros nomeados da URL (como 'id')
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Chama o método do controller, passando os parâmetros da URL
                call_user_func_array([$controller, $method], $params);
                return;
            }
        }

        Response::json(['error' => 'Endpoint Not Found'], 404);
    }
}
