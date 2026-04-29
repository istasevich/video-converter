FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    unzip \
    ffmpeg \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    mysql-client \
    sqlite-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo_mysql \
        pdo_sqlite \
        zip \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts || true

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]

ENTRYPOINT ["/var/www/html/docker/run.sh"]
