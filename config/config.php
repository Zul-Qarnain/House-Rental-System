<?php
$local = __DIR__ . '/config.local.php';
$env   = file_exists($local) ? require $local : [];

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
