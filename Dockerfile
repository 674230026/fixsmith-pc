FROM php:8.4-cli

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

# Ensure .env exists with hardcoded APP_KEY
RUN cp .env.example .env && sed -i 's|^APP_KEY=.*|APP_KEY=base64:Wu9mvtRrD7ilmEA3RgP/qKRiV2m8ITE1aqiqubEjVH4=|' .env

# Install Composer dependencies with ignore-platform-reqs
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

ENV PORT=10000
ENV APP_NAME="Fixsmith PC Repair Studio"
ENV APP_KEY="base64:Wu9mvtRrD7ilmEA3RgP/qKRiV2m8ITE1aqiqubEjVH4="
ENV APP_ENV=production
ENV APP_DEBUG=true
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/app/database/database.sqlite
ENV SESSION_DRIVER=file
ENV CACHE_STORE=file

EXPOSE 10000

CMD ["sh", "-c", "touch database/database.sqlite && php artisan migrate --force && php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
