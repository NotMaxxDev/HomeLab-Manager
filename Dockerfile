FROM php:8.4-fpm-alpine

# System-Abhängigkeiten und PHP-Extensions installieren
RUN apk add --no-linux-headers --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    nodejs \
    npm \
    fcgi

RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dateien kopieren
COPY . .

# PHP & JS Abhängigkeiten installieren & Assets bauen
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build

EXPOSE 8000

CMD ["sh", "-c", "php artisan key:generate --force && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
