# Gunakan PHP 8.1 FPM
FROM php:8.1-fpm

# Install dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel & Filament
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

# Set Working Directory
WORKDIR /var/www/html

# Salin seluruh kode aplikasi ke dalam container
# Pastikan folder 'public/build' sudah ada di lokal dan ter-push ke GitHub
COPY . .

# Konfigurasi Nginx
# Menggunakan cp untuk memindahkan file nginx.conf ke lokasi default Nginx
RUN cp nginx.conf /etc/nginx/sites-available/default

# Install dependensi PHP (Composer)
# Kita tidak menjalankan 'npm install' & 'npm run build' di sini untuk menghemat RAM Render
RUN composer install --no-dev --optimize-autoloader

# Beri hak akses ke folder yang dibutuhkan Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan Nginx dan PHP-FPM secara bersamaan
CMD service nginx start && php-fpm