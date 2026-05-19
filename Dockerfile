FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

EXPOSE 8000

CMD php artisan migrate --force && \
    php artisan queue:work --daemon --tries=3 --timeout=30 & \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
