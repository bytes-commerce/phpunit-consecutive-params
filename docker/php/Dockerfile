FROM php:8.2.7-zts-alpine3.18

WORKDIR /var/www/html/
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add --update linux-headers
RUN apk add -u \
        nano \
        wget \
        unzip \
        autoconf \
        gcc \
        g++ \
        make

COPY ./composer.json /var/www/html/composer.json
COPY ./src /var/www/html/src
COPY ./tests /var/www/html/tests
COPY ./ecs.php /var/www/html/ecs.php
COPY ./phpstan.neon /var/www/html/phpstan.neon

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY ./docker/php/999-php-custom.ini /usr/local/etc/php/conf.d/999-php-custom.ini

RUN composer install --no-interaction --no-progress --optimize-autoloader

CMD ["tail", "-F", "/dev/null"]
