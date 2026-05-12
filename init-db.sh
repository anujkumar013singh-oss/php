#!/bin/bash

echo "Creating database and schema..."

# Connect to MySQL server and create database/schema in one go
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS << 'EOF'
CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

-- Create users table if it doesn't exist
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin if not exists
INSERT IGNORE INTO users (name, email, password, role) VALUES (
    'Anuj',
    'alonesurvivor03@gmail.com',
    '$2y$12$qv3yPHxaR/mwysFII9RPiuiQy108on59CWDWMtQZRU9Jqn1.eJB0.',
    'admin'
);
EOF

if [ $? -eq 0 ]; then
    echo "Database and schema created successfully!"
    echo "Default admin account created:"
    echo "Email: alonesurvivor03@gmail.com"
    echo "Password: Admin@123"
else
    echo "ERROR: Failed to create database"
    echo "DB_HOST: $DB_HOST"
    echo "DB_USER: $DB_USER"
    echo "DB_NAME: $DB_NAME"
fi
