<?php
// Working users endpoint with demo data fallback
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

session_start();
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$role = $_GET['role'] ?? 'all';

try {
    // Try database connection first
    $dsn = "mysql:host=" . ($_ENV['DB_HOST'] ?? 'localhost') . ";dbname=" . ($_ENV['DB_NAME'] ?? 'user_management') . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    $sql = "SELECT * FROM users";
    $params = [];
    
    if ($role === 'admin') {
        $sql .= " WHERE role = 'admin'";
    } elseif ($role === 'user') {
        $sql .= " WHERE role = 'user'";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    
    $summary = [
        'total' => count($users),
        'admin' => count(array_filter($users, fn($u) => $u['role'] === 'admin')),
        'user' => count(array_filter($users, fn($u) => $u['role'] === 'user'))
    ];
    
    echo json_encode([
        'success' => true,
        'total' => $summary['total'],
        'summary' => $summary,
        'users' => $users
    ]);
    
} catch (Exception $e) {
    // Fallback to demo data
    $demoUsers = [
        [
            'id' => 1,
            'name' => 'Anuj',
            'email' => 'alonesurvivor03@gmail.com',
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 2,
            'name' => 'Demo User',
            'email' => 'user@example.com',
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    if ($role === 'admin') {
        $filteredUsers = array_filter($demoUsers, fn($u) => $u['role'] === 'admin');
    } elseif ($role === 'user') {
        $filteredUsers = array_filter($demoUsers, fn($u) => $u['role'] === 'user');
    } else {
        $filteredUsers = $demoUsers;
    }
    
    $summary = [
        'total' => count($filteredUsers),
        'admin' => count(array_filter($filteredUsers, fn($u) => $u['role'] === 'admin')),
        'user' => count(array_filter($filteredUsers, fn($u) => $u['role'] === 'user'))
    ];
    
    echo json_encode([
        'success' => true,
        'total' => $summary['total'],
        'summary' => $summary,
        'users' => array_values($filteredUsers),
        'demo_mode' => true,
        'message' => 'Showing demo data (database unavailable)'
    ]);
}
?>
