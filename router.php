<?php
// router.php - Router script for PHP built-in server

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set CORS headers for all requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string from request URI
$path = parse_url($request_uri, PHP_URL_PATH);

// Route to appropriate files
switch ($path) {
    case '/':
    case '/index':
    case '/index.html':
        // Reset headers for HTML
        header_remove('Content-Type');
        header('Content-Type: text/html; charset=UTF-8');
        include 'index.html';
        break;
        
    case '/login':
    case '/login.php':
        include 'login.php';
        break;
        
    case '/login-simple':
    case '/login-simple.php':
        include 'login-simple.php';
        break;
        
    case '/register':
    case '/register.php':
        include 'register.php';
        break;
        
    case '/logout':
    case '/logout.php':
        include 'logout.php';
        break;
        
    case '/users':
    case '/users.php':
        include 'users.php';
        break;
        
    case '/debug':
    case '/debug.php':
        include 'debug.php';
        break;
        
    case '/create-db':
    case '/create-db.php':
        include 'create-db.php';
        break;
        
    case '/test':
    case '/test.php':
        include 'test.php';
        break;
        
    default:
        // Serve static files if they exist
        $file_path = __DIR__ . $path;
        if (file_exists($file_path) && is_file($file_path)) {
            $mime_type = mime_content_type($file_path);
            header_remove('Content-Type');
            header("Content-Type: $mime_type");
            readfile($file_path);
        } else {
            // Return detailed 404 for unknown routes
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Not found',
                'path' => $path,
                'method' => $request_method,
                'available_routes' => [
                    '/' => 'index.html',
                    '/login.php' => 'Login endpoint',
                    '/register.php' => 'Register endpoint',
                    '/logout.php' => 'Logout endpoint',
                    '/users.php' => 'Users endpoint',
                    '/debug.php' => 'Debug endpoint',
                    '/create-db.php' => 'Database creation endpoint'
                ]
            ]);
        }
        break;
}
?>
