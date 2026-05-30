# Gunakan PHP 8.1
FROM php:8.1-fpm

# Install dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js untuk build aset (Vite/Filament)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Konfigurasi Nginx
COPY ./nginx.conf /etc/nginx/sites-available/default

# Salin kode aplikasi
COPY . /var/www/html
WORKDIR /var/www/html

# Install dependensi Laravel & Build aset
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Beri hak akses ke storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan Nginx dan PHP-FPM
CMD service nginx start && php-fpm
