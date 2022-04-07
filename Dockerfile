FROM php:8.0-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN set -ex \
    	&& apk --no-cache add $PHPIZE_DEPS postgresql-dev nodejs yarn npm\
    	&& docker-php-ext-install pdo pdo_pgsql

RUN pecl install redis \
        && docker-php-ext-enable redis

WORKDIR /var/www
