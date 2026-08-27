<?php
class Router {
    private array $routes = [];

    public function add(string $method, string $path, string $handler): void {
        $pattern = preg_replace('/:[a-zA-Z_]+/', '([^/]+)', $path);
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                [$controllerName, $action] = explode('@', $route['handler']);
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $action)) {
                        call_user_func_array([$controller, $action], $matches);
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        echo "404 Page Not Found";
    }
}
