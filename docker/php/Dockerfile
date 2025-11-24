FROM php:8.1-fpm

RUN apt update \
    && apt-get install -y nano git curl libxml2-dev librabbitmq-dev zlib1g-dev libicu-dev g++ iputils-ping libzip-dev zip unzip libpq-dev \
    && pecl install xdebug-3.1.5 && docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Install Composer (official recommended method)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

COPY composer.sh /usr/local/bin/_composer
RUN chmod +x /usr/local/bin/_composer

COPY examples.sh /usr/local/bin/_examples
RUN chmod +x /usr/local/bin/_examples

WORKDIR /var/www
