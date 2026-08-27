<?php
function runSqlFile(PDO $pdo, string $filepath) {
    $sql = file_get_contents($filepath);
    $lines = explode("\n", $sql);
    $delimiter = ";";
    $buffer = "";
    
    foreach ($lines as $line) {
        $trim = trim($line);
        if (strpos($trim, "--") === 0) continue;
        if (preg_match("/^DELIMITER\s+(.+)$/i", $trim, $m)) {
            $delimiter = trim($m[1]);
            continue;
        }
        if (empty($trim) && empty(trim($buffer))) continue;
        
        $buffer .= $line . "\n";
        if (str_ends_with($trim, $delimiter)) {
            $query = trim(substr(trim($buffer), 0, -strlen($delimiter)));
            if (!empty($query)) {
                $pdo->exec($query);
            }
            $buffer = "";
        }
    }
}

$config = require __DIR__ . '/../config/config.php';
$db = $config['db'];
$dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
if (!empty($db['ssl_ca'])) {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $db['ssl_ca'];
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
}

try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], $options);
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $views = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($views as $v) { $pdo->exec("DROP VIEW IF EXISTS `$v`"); }
    $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) { $pdo->exec("DROP TABLE IF EXISTS `$t` CASCADE"); }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    runSqlFile($pdo, __DIR__ . '/schema.sql');
    runSqlFile($pdo, __DIR__ . '/seed.sql');
    
    echo "Database schema and seed data loaded successfully!\n";
} catch (Exception $e) {
    echo "Error loading DB: " . $e->getMessage() . "\n";
    exit(1);
}
