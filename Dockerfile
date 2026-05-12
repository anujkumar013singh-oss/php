FROM php:8.2-apache

# Install required packages
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all application files
COPY . .

# Make scripts executable
RUN chmod +x init-db.sh start-apache.sh

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the port (Render will set this via PORT env var)
EXPOSE 10000

# Start Apache with port configuration and database initialization
CMD ["./start-apache.sh"]
