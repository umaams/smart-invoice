# ---------------------------------------
# Stage 1: Build dependencies (Composer)
# ---------------------------------------
FROM php:7.4-cli AS builder

# Install required packages
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd zip exif

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && ls -la /app/vendor

# Copy seluruh project
COPY . .

# ---------------------------------------
# Stage 2: Production Image (PHP-FPM)
# ---------------------------------------
FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql zip gd exif opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy app dari builder
COPY --from=builder /app /var/www/html

# CI3 membutuhkan permission writable directories
RUN chown -R www-data:www-data application/cache \
    && chown -R www-data:www-data application/logs \
    && chmod -R 775 application/cache application/logs

EXPOSE 9000

CMD ["php-fpm"]
