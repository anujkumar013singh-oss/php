<?php
// logout.php - Destroys session
require_once 'config.php';

$_SESSION = [];
session_destroy();

echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
