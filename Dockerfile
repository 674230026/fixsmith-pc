FROM php:8.3-cli-alpine

RUN apk add --no-cache sqlite-dev git unzip curl libzip-dev \
    && docker-php-ext-install pdo_sqlite pcntl bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy the Laravel application
COPY laravel-app/ .

# Ensure .env exists
RUN cp .env.example .env

# Install Composer dependencies with no-scripts to prevent early artisan errors
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Dump optimized autoload
RUN composer dump-autoload --optimize

ENV PORT=10000
EXPOSE 10000

CMD ["sh", "-c", "php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
