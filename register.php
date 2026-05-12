<?php
// register.php - User Registration API
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// ── Frontend & Backend Validation ────────────────────────────────────────────

$name  = isset($data['name'])  ? trim($data['name'])  : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$pass  = isset($data['password']) ? $data['password'] : '';
$role  = isset($data['role'])  ? trim($data['role'])  : 'user';

// Required fields
if (empty($name) || empty($email) || empty($pass)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name, email, and password are required.']);
    exit;
}

// Name length
if (strlen($name) < 2 || strlen($name) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name must be between 2 and 100 characters.']);
    exit;
}

// Email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address format.']);
    exit;
}

// Password strength: min 8 chars, at least one letter and one number
if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/[0-9]/', $pass)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters and include letters and numbers.']);
    exit;
}

// Role validation
if (!in_array($role, ['admin', 'user'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid role. Must be admin or user.']);
    exit;
}

$db = getDB();

// ── Duplicate Email Check ─────────────────────────────────────────────────────
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'This email is already registered. Please use a different email or log in.']);
    exit;
}

// ── Single Admin Constraint ───────────────────────────────────────────────────
if ($role === 'admin') {
    $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM users WHERE role = "admin"');
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row['cnt'] >= 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'An admin already exists in the system. Only one admin is allowed.']);
        exit;
    }
}

// ── Create User ───────────────────────────────────────────────────────────────
$hashedPassword = password_hash($pass, PASSWORD_BCRYPT);

$stmt = $db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
$stmt->execute([$name, $email, $hashedPassword, $role]);

$userId = $db->lastInsertId();

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Registration successful! You can now log in.',
    'user' => [
        'id'   => $userId,
        'name' => $name,
        'email'=> $email,
        'role' => $role
    ]
]);
