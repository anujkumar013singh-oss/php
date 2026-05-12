<?php
// config.php - Database configuration
// Update these values with your hosting credentials

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'user_management');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');       // Change to your DB username
define('DB_PASS', $_ENV['DB_PASS'] ?? '');           // Change to your DB password
define('DB_CHARSET', 'utf8mb4');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT           => 30,
        ];
        
        // Retry connection up to 5 times with delays
        $maxRetries = 5;
        $retryDelay = 2; // seconds
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                break; // Success, exit retry loop
            } catch (PDOException $e) {
                if ($attempt === $maxRetries) {
                    // Final attempt failed, return detailed error
                    http_response_code(500);
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Database connection failed',
                        'error' => $e->getMessage(),
                        'attempt' => $attempt,
                        'host' => DB_HOST,
                        'database' => DB_NAME
                    ]);
                    exit;
                }
                // Wait before retry
                sleep($retryDelay);
                $retryDelay *= 2; // Exponential backoff
            }
        }
    }
    return $pdo;
}

// CORS headers (for development; restrict in production)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Simple session-based auth helper
session_start();

function requireAuth() {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
        exit;
    }
}
