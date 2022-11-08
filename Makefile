.DEFAULT_GOAL := help
.SILENT:
.PHONY: vendor

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

##################
# Useful targets #
##################

## Install all install_* requirements and launch project.
install: env_file env_run install_vendor db_install

## Run project, install vendors and run migrations.
run: env_run install_vendor db_install

## Stop project.
stop:
	docker-compose stop

## Down project and remove volumes (databases).
down:
	docker-compose down -v --remove-orphans

## Run all quality assurance tools (tests and code inspection).
qa: code_static_analysis code_fixer code_detect code_correct test_spec test test_behaviour

## Truncate database and import fixtures.
fixtures: down run import_dev

cache_clear:
	docker-compose exec php bin/console cache:clear

ssh: bash

bash:
	docker-compose exec -it php sh

########
# Code #
########

## Run codesniffer to correct violations of a defined coding project standards.
code_correct:
	docker-compose exec php bin/phpcs --standard=PSR2 src

## Run codesniffer to detect violations of a defined coding project standards.
code_detect:
	docker-compose exec php bin/phpcbf --standard=PSR2 src tests

## Run cs-fixer to fix php code to follow project standards.
code_fixer:
	docker-compose exec php bin/php-cs-fixer fix

## Run PHPStan to find errors in code.
code_static_analysis:
	docker-compose exec php bin/phpstan analyse src --level max

###############
# Translation #
###############

translation_extract:
	docker-compose exec php bin/console translation:extract --force --prefix="" en

translation_push:
	docker-compose exec php bin/console translation:push --force crowdin --locales=en --domains=messages

translation_pull:
	docker-compose exec php bin/console translation:pull --force crowdin --locales=es-ES --locales=fr --locales=pt-PT --locales=en --domains=messages
	mv translations/messages.es-ES.xlf translations/messages.es.xlf
	mv translations/messages.pt-PT.xlf translations/messages.pt.xlf

###############
# Environment #
###############

## Set defaut environment variables by copying env.dist file as .env.
env_file:
	cp .env.dist .env

## Launch docker environment.
env_run:
	docker-compose up -d

###########
# Install #
###########

## Install vendors.
install_vendor:
	docker-compose exec php composer install --prefer-dist --no-scripts --no-progress --no-suggest

## Update vendors.
update_vendor:
	docker-compose run --rm php php -d memory_limit=-1 /usr/bin/composer update

## Install assets.
install_assets:
	docker-compose exec php bin/console assets:install -n

install_scripts:
	docker-compose run --rm php composer auto-scripts

########
# Test #
########

## Run unit&integration tests with pre-installing test database.
test: db_install_test test_unit

## Run unit&integration tests.
test_unit:
	docker-compose exec php bin/phpunit

################################
# Doctrine / DB / Migrations   #
################################

## Migrate diff db
db_diff:
	 docker-compose exec php bin/console do:mi:di -n

## Run database migration.
db_install:
	docker-compose exec php bin/console do:mi:mi -n

## Run test database migration.
db_create_test:
	docker-compose exec php bin/console do:database:create -n --env=test

## Run test database migration.
db_install_test:
	docker-compose exec php bin/console do:mi:mi -n --env=test

################
# Redis        #
################

## Redis flush all
redis_flushall:
	docker-compose exec php bin/console redis:flushall -n

## Redis flush doctrine client
redis_flush_doctrine:
	docker-compose exec php bin/console redis:flushdb -n --client=doctrine

## Redis flush session client
redis_flush_sessions:
	docker-compose exec php bin/console redis:flushdb -n --client=session

## Redis flush cache
redis_flush_cache:
	docker-compose exec php bin/console redis:flushdb -n --client=default

################
# RabbitMQ     #
################

## Install queues and fabrics.
rabbit_fabrics:
	docker-compose exec php bin/console rabbitmq:setup-fabric

################
# Messages     #
################

## Consume messages
messenger_consume:
	docker-compose exec php bin/console messenger:consume async --limit=1000 -vvv
