#!/bin/sh
set -e -m

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

# Run supervisord for messenger
#supervisord &

chown -R www-data:www-data var

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