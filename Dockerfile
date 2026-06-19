FROM php:8.1-fpm

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    nginx libpq-dev git unzip libzip-dev libicu-dev \
    libonig-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip intl mbstring xml gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Konfigurasi Nginx
RUN cp nginx.conf /etc/nginx/sites-available/default

# Install dependensi
RUN composer install --no-dev --optimize-autoloader

# PENTING: Beri akses ke folder public agar tidak 403
RUN chown -R www-data:www-data /var/www/html/public /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache



# Perbaiki start.sh: Jalankan Nginx di depan agar error terlihat di log
RUN echo '#!/bin/bash\n\
php artisan migrate --force\n\
php artisan db:seed --force\n\
php artisan storage:link --force\n\
service nginx start\n\
php-fpm' > /start.sh && chmod +x /start.sh

EXPOSE 80
# Gunakan skrip sebagai entrypoint
CMD ["/start.sh"]
