<?php
// Simple test endpoint to diagnose 500 errors
header('Content-Type: application/json');

try {
    // Test basic PHP functionality
    $response = [
        'success' => true,
        'message' => 'PHP is working!',
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'server_info' => [
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'unknown',
        ],
        'environment' => [
            'DB_HOST' => $_ENV['DB_HOST'] ?? 'NOT_SET',
            'DB_NAME' => $_ENV['DB_NAME'] ?? 'NOT_SET',
            'DB_USER' => $_ENV['DB_USER'] ?? 'NOT_SET',
            'DB_PASS' => $_ENV['DB_PASS'] ?? 'NOT_SET',
            'PORT' => $_ENV['PORT'] ?? 'NOT_SET',
        ]
    ];
    
    // Test database connection if environment variables are set
    if (!empty($_ENV['DB_HOST']) && !empty($_ENV['DB_USER'])) {
        try {
            $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . ($_ENV['DB_NAME'] ?? 'user_management') . ";charset=utf8mb4";
            $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $response['database'] = [
                'status' => 'connected',
                'message' => 'Database connection successful'
            ];
            
            // Test if users table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            $response['database']['users_table'] = $stmt->rowCount() > 0 ? 'exists' : 'missing';
            
        } catch (Exception $e) {
            $response['database'] = [
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    } else {
        $response['database'] = [
            'status' => 'not_configured',
            'message' => 'Environment variables not set'
        ];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>
