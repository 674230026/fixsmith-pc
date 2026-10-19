FROM php:8.3-cli

# Install system dependencies & SQLite
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-install pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy the Laravel application
COPY laravel-app/ .

# Ensure .env exists
RUN cp .env.example .env

# Install Composer dependencies with ignore-platform-reqs
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

ENV PORT=10000
EXPOSE 10000

CMD ["sh", "-c", "php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
