#!/bin/bash

# Start PHP server immediately in background
echo "Starting PHP server on port ${PORT:-8000}..."
php -S 0.0.0.0:${PORT:-8000} &
PHP_PID=$!

# Initialize database in background
./init-db.sh &

# Wait for the PHP server process
wait $PHP_PID
