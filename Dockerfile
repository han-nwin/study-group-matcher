# Use official PHP with Apache
FROM php:8.2-apache

# Enable necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy project files to the container
COPY . .

# Give Apache permission to serve files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for Apache
EXPOSE 80
