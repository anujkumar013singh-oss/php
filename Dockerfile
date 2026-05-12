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

# Make init script executable
RUN chmod +x init-db.sh

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (Apache default)
EXPOSE 80

# Start database initialization and Apache
CMD ["/bin/bash", "-c", "./init-db.sh && apache2-foreground"]
