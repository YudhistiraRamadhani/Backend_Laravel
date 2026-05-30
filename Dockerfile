FROM php:8.1-fpm

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip intl mbstring xml gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Konfigurasi Nginx
RUN cp nginx.conf /etc/nginx/sites-available/default

# Install dependensi PHP
RUN composer install --no-dev --optimize-autoloader

# Atur hak akses folder agar tidak 403 Forbidden
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Skrip untuk memastikan migrasi berjalan sebelum server aktif
RUN echo '#!/bin/bash\n\
php artisan migrate --force\n\
php artisan db:seed --force\n\
nginx -g "daemon off;" &' > /start.sh && chmod +x /start.sh

# Jalankan PHP-FPM dan skrip Nginx
EXPOSE 80
CMD ["sh", "-c", "/start.sh && php-fpm"]
