<?php
abstract class Controller {
    protected function render(string $viewPath, array $data = []): void {
        extract($data);
        $file = __DIR__ . '/../views/' . $viewPath . '.php';
        if (file_exists($file)) {
            require $file;
        } else {
            http_response_code(404);
            echo "View [{$viewPath}] not found.";
        }
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}
