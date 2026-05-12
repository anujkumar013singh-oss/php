<?php
// router.php - Router script for PHP built-in server

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string from request URI
$path = parse_url($request_uri, PHP_URL_PATH);

// Route to appropriate files
switch ($path) {
    case '/':
    case '/index':
    case '/index.html':
        include 'index.html';
        break;
        
    case '/login':
    case '/login.php':
        include 'login.php';
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
        
    default:
        // Serve static files if they exist
        $file_path = __DIR__ . $path;
        if (file_exists($file_path) && is_file($file_path)) {
            $mime_type = mime_content_type($file_path);
            header("Content-Type: $mime_type");
            readfile($file_path);
        } else {
            // Return 404 for unknown routes
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        }
        break;
}
?>
