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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin seluruh kode proyek (pastikan public/build sudah ada hasil dari lokal)
COPY . .

# Konfigurasi Nginx
RUN cp nginx.conf /etc/nginx/sites-available/default

# Install dependensi PHP
RUN composer install --no-dev --optimize-autoloader

# Beri hak akses ke folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan migrasi, seeder, lalu mulai server
# Menggunakan && memastikan langkah berurutan: jika migrasi gagal, Nginx tidak akan nyala
CMD php artisan migrate:fresh --seed --force && service nginx start && php-fpm
