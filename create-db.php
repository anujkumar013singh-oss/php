<?php
// create-db.php - Create database and import schema
header('Content-Type: application/json');

try {
    // Connect to MySQL server (without database name)
    $dsn = "mysql:host=" . ($_ENV['DB_HOST'] ?? 'localhost') . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Create database if it doesn't exist
    $dbName = $_ENV['DB_NAME'] ?? 'user_management';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
    $pdo->exec("USE `$dbName`");
    
    // Read and execute schema
    $schema = file_get_contents('schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database and schema created successfully',
        'database' => $dbName
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database creation failed',
        'error' => $e->getMessage()
    ]);
}
?>
