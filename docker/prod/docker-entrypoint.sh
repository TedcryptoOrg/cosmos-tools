#!/bin/sh
set -e
set -m

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

# Run supervisord for messenger
supervisord &

chown -R www-data:www-data var

exec docker-php-entrypoint "$@"