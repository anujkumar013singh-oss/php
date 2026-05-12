<?php
// debug.php - Debug endpoint to check database connection and environment
header('Content-Type: application/json');

// Check environment variables
$env_vars = [
    'DB_HOST' => $_ENV['DB_HOST'] ?? 'NOT_SET',
    'DB_NAME' => $_ENV['DB_NAME'] ?? 'NOT_SET', 
    'DB_USER' => $_ENV['DB_USER'] ?? 'NOT_SET',
    'DB_PASS' => $_ENV['DB_PASS'] ?? 'NOT_SET',
    'PORT' => $_ENV['PORT'] ?? 'NOT_SET'
];

// Test database connection without requiring config.php
$connection_test = [
    'status' => 'not_attempted',
    'error' => null,
    'tables' => []
];

try {
    $dsn = "mysql:host=" . ($env_vars['DB_HOST']) . ";dbname=" . ($env_vars['DB_NAME']) . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $env_vars['DB_USER'], $env_vars['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    $connection_test['status'] = 'success';
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $connection_test['tables'] = $tables;
    
    // Check if users table has data
    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        $connection_test['users_count'] = $result['count'];
    }
    
} catch (Exception $e) {
    $connection_test['status'] = 'failed';
    $connection_test['error'] = $e->getMessage();
}

echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => $env_vars,
    'database' => $connection_test,
    'php_version' => PHP_VERSION,
    'server_info' => $_SERVER
], JSON_PRETTY_PRINT);
?>
