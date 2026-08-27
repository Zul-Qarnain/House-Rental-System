<?php
$local = __DIR__ . '/config.local.php';
$env   = file_exists($local) ? require $local : [];

$dotEnvPath = __DIR__ . '/../.env';
if (file_exists($dotEnvPath)) {
    $lines = file($dotEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if (!isset($env[$key])) {
                $env[$key] = $val;
            }
        }
    }
}

return [
    'db' => [
        'host' => $env['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1',
        'port' => $env['DB_PORT'] ?? getenv('DB_PORT') ?: 3306,
        'name' => $env['DB_NAME'] ?? getenv('DB_NAME') ?: 'defaultdb',
        'user' => $env['DB_USER'] ?? getenv('DB_USER') ?: 'root',
        'pass' => $env['DB_PASS'] ?? getenv('DB_PASS') ?: '',
        'ssl_ca' => $env['DB_SSL_CA'] ?? getenv('DB_SSL_CA') ?: null,
    ],
];
