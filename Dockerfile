# ---------------------------------------
# Stage 1: Build dependencies (Composer)
# ---------------------------------------
FROM php:7.4-cli AS builder

RUN apt-get update && apt-get install -y \
    curl git unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd zip exif \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php \
        --install-dir=/usr/local/bin \
        --filename=composer \
    && rm composer-setup.php

WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ---------------------------------------
# Stage 2: Production (Nginx + PHP-FPM)
# ---------------------------------------
FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mysqli zip gd exif opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY --from=builder /app /var/www/html

# Remove default nginx config
RUN rm /etc/nginx/sites-enabled/default

# Copy custom nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Permissions
RUN mkdir -p \
        application/cache \
        application/logs \
        uploads \
        vendor/mpdf/mpdf/tmp \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD service nginx start && php-fpm
