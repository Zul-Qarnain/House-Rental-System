<?php
class Auth {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            @ini_set('session.gc_probability', 0);
            @session_start();
        }
    }

    public static function login(array $user): void {
        self::start();
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'is_verified' => (bool)$user['is_verified'],
        ];
    }

    public static function logout(): void {
        self::start();
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function user(): ?array {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool {
        return self::user() !== null;
    }

    public static function requireRole(string ...$roles): void {
        self::start();
        $user = self::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }
        if (!in_array($user['role'], $roles, true)) {
            http_response_code(403);
            echo "403 Forbidden: Access restricted to " . implode('/', $roles);
            exit;
        }
    }

    public static function csrfToken(): string {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool {
        self::start();
        return !empty($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
