#!/bin/bash

# Initialize database first
./init-db.sh

# Start PHP built-in server on Render's PORT
echo "Starting PHP server on port ${PORT:-8000}..."
exec php -S 0.0.0.0:${PORT:-8000}
