FROM php:8.4-fpm

RUN apt-get update && apt-get install -y curl gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y \
    libzip-dev \
    libpng-dev \
    postgresql-client \
    libpq-dev \
    nodejs \
    unzip \
    git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install PHP extensions
RUN docker-php-ext-install pcntl pdo pdo_mysql gd bcmath zip opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /usr/share/nginx/html/

COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && sed 's_@php artisan package:discover_/bin/true_;' -i composer.json \
    && composer install --ignore-platform-req=php --no-dev --optimize-autoloader \
    && composer clear-cache \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

COPY ./scripts/php-fpm-entrypoint /usr/local/bin/php-entrypoint

RUN chmod a+x /usr/local/bin/*

ENTRYPOINT ["/usr/local/bin/php-entrypoint"]

CMD ["php-fpm"]
