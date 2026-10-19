FROM php:8.3-cli-alpine

RUN apk add --no-cache sqlite-dev git unzip
RUN docker-php-ext-install pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy the Laravel application
COPY laravel-app/ .

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Set environment
ENV PORT=10000
EXPOSE 10000

CMD ["sh", "-c", "php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
