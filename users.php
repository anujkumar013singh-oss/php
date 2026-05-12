<?php
// users.php - User Listing API (protected)
require_once 'config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$db = getDB();

// Optional filter: ?role=admin
$role = isset($_GET['role']) ? trim($_GET['role']) : null;

if ($role && in_array($role, ['admin', 'user'])) {
    $stmt = $db->prepare('SELECT id, name, email, role, created_at FROM users WHERE role = ? ORDER BY created_at DESC');
    $stmt->execute([$role]);
} else {
    $stmt = $db->prepare('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
    $stmt->execute();
}

$users = $stmt->fetchAll();

// Count summary
$adminCount = 0;
$userCount  = 0;
foreach ($users as $u) {
    if ($u['role'] === 'admin') $adminCount++;
    else $userCount++;
}

echo json_encode([
    'success' => true,
    'total'   => count($users),
    'summary' => ['admins' => $adminCount, 'users' => $userCount],
    'users'   => $users
]);
