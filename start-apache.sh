#!/bin/bash

# Configure Apache to use Render's PORT
if [ -n "$PORT" ]; then
    echo "Configuring Apache to use port $PORT..."
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf
fi

# Initialize database
./init-db.sh

# Start Apache
echo "Starting Apache on port ${PORT:-80}..."
apache2-foreground
