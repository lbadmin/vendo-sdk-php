FROM php:7.4-fpm

RUN apt update \
    && apt-get install -y nano git curl libxml2-dev librabbitmq-dev zlib1g-dev libicu-dev g++ iputils-ping libzip-dev zip unzip libpq-dev \
    && pecl install xdebug-2.9.2 && docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY composer.sh /usr/local/bin/_composer
RUN chmod +x /usr/local/bin/_composer

COPY examples.sh /usr/local/bin/_examples
RUN chmod +x /usr/local/bin/_examples

WORKDIR /var/www
