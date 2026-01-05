# --- Base Image ---
# Use an official PHP image with Apache pre-installed.
# Choose a specific PHP version (e.g., 8.2) for consistency.
# You can adjust the version (e.g., php:8.1-apache, php:8.3-apache) as needed.
FROM php:8.2-apache

# --- Install System Dependencies ---
# Install required system libraries for PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# --- Install PHP Extensions ---
# We need the PDO MySQL driver to connect to the MySQL database.
# docker-php-ext-install is a helper script included in the official PHP images.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip

# --- Set Working Directory ---
# Sets the default directory for subsequent instructions like COPY.
# /var/www/html is the default web root for Apache in this image.
WORKDIR /var/www/html

# --- Create Uploads Folder ---
# Create the uploads folder and set permissions during the image build.
RUN mkdir -p /var/www/html/uploads && chown www-data:www-data /var/www/html/uploads && chmod 755 /var/www/html/uploads

# --- Copy Application Files ---
# Copy the contents of the current directory (where the Dockerfile is)
# into the container's working directory (/var/www/html).
# This includes your PHP files, css, images, include, config, etc.
COPY . .

# --- Copy virtual host configuration ---
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# --- Optional: Set Permissions ---
# The base php:apache image usually handles permissions correctly for /var/www/html.
# If you encounter permission issues, you might need to uncomment and adjust the following lines:
# RUN chown -R www-data:www-data /var/www/html
# RUN chmod -R 755 /var/www/html

# --- Production PHP Configuration ---
# Set production PHP settings
# Note: display_errors is controlled by APP_ENV in config/db.php
RUN { \
    echo 'upload_max_filesize = 10M'; \
    echo 'post_max_size = 10M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 30'; \
    echo 'log_errors = On'; \
    echo 'error_log = /var/log/php_errors.log'; \
    echo 'session.cookie_httponly = On'; \
    echo 'session.cookie_secure = On'; \
    echo 'session.use_strict_mode = On'; \
    } > /usr/local/etc/php/conf.d/production.ini

# --- Enable Apache Modules ---
# Enable required Apache modules (mpm_prefork is already enabled in php:apache base image)
RUN a2enmod rewrite headers

# --- Create Health Check Endpoint ---
RUN echo "<?php http_response_code(200); echo 'OK'; ?>" > /var/www/html/health.php

# --- Expose Port ---
# Inform Docker that the container listens on port 80 (Apache's default HTTP port).
# Note: The actual port mapping to the host machine is done in docker-compose.yml.
EXPOSE 80

# --- Start Apache in Foreground ---
# Explicitly set the command to start Apache in foreground mode
CMD ["apache2-foreground"]