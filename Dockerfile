# ---------------------------------------
# Stage 1: Build dependencies (Composer)
# ---------------------------------------
FROM php:7.4-cli AS builder

# Install build dependencies
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd zip exif \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (SAFE)
RUN curl -fsSL https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php \
        --install-dir=/usr/local/bin \
        --filename=composer \
    && rm composer-setup.php

WORKDIR /app

# Copy entire application first
COPY . .

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# ---------------------------------------
# Stage 2: Production Image (PHP-FPM)
# ---------------------------------------
FROM php:7.4-fpm

# Install runtime dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mysqli zip gd exif opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application from builder
COPY --from=builder /app /var/www/html

# ---------------------------------------
# Permissions (CI3 + mPDF)
# ---------------------------------------
RUN mkdir -p \
        application/cache \
        application/logs \
        vendor/mpdf/mpdf/tmp \
    && chown -R www-data:www-data \
        application/cache \
        application/logs \
        vendor/mpdf/mpdf/tmp \
    && chmod -R 775 \
        application/cache \
        application/logs \
        vendor/mpdf/mpdf/tmp

# Run as non-root (BEST PRACTICE)
USER www-data

EXPOSE 9000

CMD ["php-fpm"]
