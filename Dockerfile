# Gunakan PHP 8.1
FROM php:8.1-fpm

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set Working Directory terlebih dahulu
WORKDIR /var/www/html

# Salin seluruh kode aplikasi ke dalam container
COPY . .

# Konfigurasi Nginx (Pastikan nginx.conf ada di root)
# Menggunakan jalur absolut agar lebih aman
COPY nginx.conf /etc/nginx/sites-available/default

# Install dependensi Laravel & Build aset
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Beri hak akses ke storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan Nginx dan PHP-FPM
CMD service nginx start && php-fpm
