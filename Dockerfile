# Gunakan image PHP 8.1 FPM
FROM php:8.1-fpm

# Install dependensi sistem dan ekstensi PHP
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

# Install Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin seluruh kode proyek
COPY . .

# Konfigurasi Nginx
RUN cp nginx.conf /etc/nginx/sites-available/default

# Install dependensi PHP tanpa dev (lebih ringan untuk produksi)
RUN composer install --no-dev --optimize-autoloader

# Pastikan user www-data memiliki hak akses ke folder storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Buat skrip start.sh untuk menjalankan migrasi, seeding, dan service
# Menggunakan 'exec' di baris terakhir agar PHP-FPM menangkap sinyal dari sistem
RUN echo '#!/bin/bash\n\
php artisan migrate --force\n\
php artisan db:seed --force\n\
service nginx start\n\
exec php-fpm' > /start.sh && chmod +x /start.sh

# Ekspos port 80 untuk web
EXPOSE 80

# Jalankan skrip start.sh sebagai pintu masuk utama
CMD ["/start.sh"]
