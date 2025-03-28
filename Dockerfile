FROM php:8.2-fpm

RUN echo "deb http://deb.debian.org/debian bullseye main" > /etc/apt/sources.list \
    && apt-get update && apt-get install -y \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        libmariadb-dev \
        supervisor \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath \
    && pecl install apcu && docker-php-ext-enable apcu \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "memory_limit=2G" > /usr/local/etc/php/conf.d/memory.ini
ENV COMPOSER_PROCESS_TIMEOUT=1200

WORKDIR /var/www

COPY composer.json ./
RUN composer install --no-scripts --no-interaction --prefer-dist --apcu-autoloader

COPY . /var/www

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod 755 /var/www

COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
