#!/bin/bash

# Initialize database first
./init-db.sh

# Start PHP server with router
echo "Starting PHP server on port ${PORT:-8000} with router..."
exec php -S 0.0.0.0:${PORT:-8000} router.php
