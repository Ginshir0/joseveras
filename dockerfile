# --- Base Image ---
# Use PHP-FPM Alpine for lightweight image without Apache MPM issues
FROM php:8.3-fpm-alpine

# --- Install System Dependencies ---
# Install required system libraries for PHP extensions (Alpine uses apk)
RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    curl-dev \
    nginx \
    gettext

# --- Install PHP Extensions ---
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip

# --- Set Working Directory ---
WORKDIR /var/www/html

# --- Create Uploads Folder ---
RUN mkdir -p /var/www/html/uploads && chown www-data:www-data /var/www/html/uploads && chmod 755 /var/www/html/uploads

# --- Copy Application Files ---
COPY . .

# --- Production PHP Configuration ---
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

# --- Configure Nginx ---
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf.template

# --- Create startup script ---
RUN printf '#!/bin/sh\nexport PORT=${PORT:-80}\nenvsubst "\$PORT" < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf\nphp-fpm -D\nnginx -g "daemon off;"\n' > /start.sh \
    && chmod +x /start.sh

# --- Create Health Check Endpoint ---
RUN echo "<?php http_response_code(200); echo 'OK'; ?>" > /var/www/html/health.php

# --- Set permissions ---
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["/start.sh"]