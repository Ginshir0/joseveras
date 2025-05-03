# --- Base Image ---
# Use an official PHP image with Apache pre-installed.
# Choose a specific PHP version (e.g., 8.2) for consistency.
# You can adjust the version (e.g., php:8.1-apache, php:8.3-apache) as needed.
FROM php:8.2-apache

# --- Install PHP Extensions ---
# We need the PDO MySQL driver to connect to the MySQL database.
# docker-php-ext-install is a helper script included in the official PHP images.
RUN docker-php-ext-install pdo_mysql

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

# --- Optional: Set Permissions ---
# The base php:apache image usually handles permissions correctly for /var/www/html.
# If you encounter permission issues, you might need to uncomment and adjust the following lines:
# RUN chown -R www-data:www-data /var/www/html
# RUN chmod -R 755 /var/www/html

# --- Expose Port ---
# Inform Docker that the container listens on port 80 (Apache's default HTTP port).
# Note: The actual port mapping to the host machine is done in docker-compose.yml.
EXPOSE 80

# --- Apache Configuration (Optional) ---
# The base image already configures Apache to run.
# If you needed to enable Apache modules like rewrite, you would add RUN commands here:
# RUN a2enmod rewrite

# The base image's default command starts Apache in the foreground,
# so you typically don't need to specify a CMD or ENTRYPOINT here.