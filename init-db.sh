#!/bin/bash

# Wait for MySQL to be ready with retry logic
echo "Waiting for database connection..."
max_attempts=30
attempt=1

while [ $attempt -le $max_attempts ]; do
    if mysql -h$DB_HOST -u$DB_USER -p$DB_PASS -e "SELECT 1" > /dev/null 2>&1; then
        echo "Database connection successful on attempt $attempt"
        break
    else
        echo "Attempt $attempt/$max_attempts: Database not ready, waiting..."
        sleep $attempt
        attempt=$((attempt + 1))
    fi
done

if [ $attempt -gt $max_attempts ]; then
    echo "ERROR: Could not connect to database after $max_attempts attempts"
    echo "DB_HOST: $DB_HOST"
    echo "DB_USER: $DB_USER"
    echo "DB_NAME: $DB_NAME"
    exit 1
fi

echo "Database is ready. Importing schema..."

# Import the database schema
if mysql -h$DB_HOST -u$DB_USER -p$DB_PASS $DB_NAME < /var/www/html/schema.sql; then
    echo "Database schema imported successfully!"
else
    echo "ERROR: Failed to import database schema"
    exit 1
fi
