#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for database connection..."
while ! mysql -h$DB_HOST -u$DB_USER -p$DB_PASS -e "SELECT 1" > /dev/null 2>&1; do
    sleep 2
done

echo "Database is ready. Importing schema..."

# Import the database schema
mysql -h$DB_HOST -u$DB_USER -p$DB_PASS < /var/www/html/schema.sql

echo "Database schema imported successfully!"
