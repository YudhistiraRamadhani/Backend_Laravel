FROM php:8.1-fpm

# Install dependensi sistem + ekstensi yang dibutuhkan Laravel & Filament
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

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

# Salin semua kode (pastikan nginx.conf ada di root repository)
COPY . .

# Copy nginx.conf
RUN cp nginx.conf /etc/nginx/sites-available/default

# Install dependensi
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Beri hak akses
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD service nginx start && php-fpm
