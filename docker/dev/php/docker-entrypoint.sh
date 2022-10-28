#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	#if [ "$APP_ENV" != 'prod' ]; then
	#	composer install --prefer-dist --no-progress --no-suggest --no-interaction
	#	bin/console assets:install
	#	bin/console doctrine:migration:migrate --no-interaction
	#	bin/console doctrine:cache:clear-metadata
	#fi

	# Permissions hack because setfacl does not work on Mac and Windows
	chown -R www-data var
fi

# xdebug config
if [ -f /usr/local/etc/php/conf.d/xdebug.ini ]
then
    # if XDEBUG_HOST is manually set
    HOST="$XDEBUG_HOST"

    # else if check if is Docker for Mac
    if [ -z "$HOST" ]; then
        HOST=`getent hosts docker.for.mac.localhost | awk '{ print $1 }'`
    fi

    # else get host ip
    if [ -z "$HOST" ]; then
        HOST=`/sbin/ip route|awk '/default/ { print $3 }'`
    fi

    sed -i "s/xdebug\.remote_host \=.*/xdebug\.remote_host\=$HOST/g" /usr/local/etc/php/conf.d/xdebug.ini
fi

exec docker-php-entrypoint "$@"