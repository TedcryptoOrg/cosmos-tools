FROM composer:2.3
FROM php:8-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache --virtual .persistent-deps \
        supervisor

RUN install-php-extensions \
    intl \
    pdo_mysql \
    zip \
    xdebug \
    pcntl

COPY --from=0 /usr/bin/composer /usr/bin/composer

COPY docker/dev/supervisord.conf /etc/supervisord.conf
COPY docker/dev/php/conf.d/php.ini /usr/local/etc/php/php.ini
COPY docker/dev/php/conf.d/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini
COPY docker/dev/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY docker/dev/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN chmod +x /usr/local/bin/docker-entrypoint

WORKDIR /srv
ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

# Prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress \
	&& composer clear-cache

COPY . ./

ENV PHP_IDE_CONFIG="serverName=localhost"

RUN mkdir -p var/cache var/logs var/sessions \
	&& composer dump-autoload --classmap-authoritative --no-dev \
	&& chown -R www-data:www-data var
