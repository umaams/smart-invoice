# ---------------------------------------
# Stage 1: Build dependencies (Composer)
# ---------------------------------------
FROM php:7.4-cli AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd zip exif \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    --install-dir=/usr/local/bin \
    --filename=composer

WORKDIR /app

# Copy composer files first (for layer cache)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Copy entire application
COPY . .

# ---------------------------------------
# Stage 2: Production Image (PHP-FPM)
# ---------------------------------------
FROM php:7.4-fpm

# Install runtime dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mysqli zip gd exif opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy app from builder stage
COPY --from=builder /app /var/www/html

# ---------------------------------------
# FIX PERMISSIONS (CI3 + mPDF)
# ---------------------------------------

# CI3 writable directories
RUN mkdir -p application/cache application/logs uploads \
    && chown -R www-data:www-data \
        application/cache \
        application/logs \
        uploads \
    && chmod -R 775 \
        application/cache \
        application/logs \
        uploads

# mPDF tmp directory (MANDATORY)
RUN mkdir -p vendor/mpdf/mpdf/tmp \
    && chown -R www-data:www-data vendor/mpdf/mpdf/tmp \
    && chmod -R 775 vendor/mpdf/mpdf/tmp

# Use non-root user
USER www-data

EXPOSE 9000

CMD ["php-fpm"]
