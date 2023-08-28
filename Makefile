.DEFAULT_GOAL := help
.SILENT:
.PHONY: vendor

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

OS_ARCH := $(shell uname -p | tr A-Z a-z)
ifeq ($(OS_ARCH),arm)
	ARM = true
else
	ARM = false
endif

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		categoryMessage = match(lastLine, /^## \[(.*)\]/); \
		categoryLength = 0; \
		if (categoryMessage) { \
			categoryName = substr(lastLine, RSTART + 4, RLENGTH - 5); \
			categoryLength = length(categoryName) + 2; \
			if (!printedCategory[categoryName]) { \
				printedCategory[categoryName] = 1; \
				printf "\n${COLOR_COMMENT}%s:${COLOR_RESET}\n", categoryName; \
			} \
		} \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3 + categoryLength, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)


##################
# Docker compose #
##################

## [Docker Compose] Prepare project environment file
.env:
	cp .env.dist .env

## [Docker Compose] Start container(s)
up: .env
ifeq ($(OS_ARCH), arm)
	docker compose -f docker-compose.yml -f docker-compose.mac.yaml up -d $(SERVICE)
else
	docker compose up -d $(SERVICE)
endif

## [Docker Compose] Build services
build: .env
	docker compose build --build-arg USER_ID=$(shell id -u) --build-arg GROUP_ID=$(shell id -g) --build-arg XDEBUG=$(XDEBUG) --no-cache

## [Docker Compose] Stop containers
stop:
	docker compose stop $(ARG)

## [Docker Compose] Stop and remove containers, networks, images, and volumes
down:
	docker compose down -v --remove-orphans

###############
# PHP TARGETS #
###############

PHP_SERVICE ?= php
PHP_RUN ?= docker compose exec $(PHP_SERVICE)

## [PHP Docker] Run any command in the PHP container
php-run:
	$(PHP_RUN) $(COMMAND)

## [PHP Docker] Run interactive shell in the PHP container
php-shell:
	$(PHP_RUN) sh

## [PHP Docker] Check latest logs from the PHP container
php-logs:
	docker compose logs $(ARG) $(PHP_SERVICE)

## [Composer] Install vendors
composer-install:
	$(PHP_RUN) composer install $(arg)

## [Composer] Update vendors
composer-update:
	$(PHP_RUN) composer update $(arg)

## [Composer] Run any composer command
composer:
	$(PHP_RUN) composer $(arg)

## [Composer] Run phpstan
phpstan:
	$(PHP_RUN) composer run phpstan

## [Composer] Run code style checks
cs-check:
	$(PHP_RUN) composer run code-style:check

## [Composer] Run code style fixer
cs-fix:
	$(PHP_RUN) composer run code-style:fix

## [Composer] Run rector checks
rector-check:
	$(PHP_RUN) composer run rector:check

## [Composer] Run rector fixer
rector-fix:
	$(PHP_RUN) composer run rector:fix

## [Composer] Run tests
tests:
	$(PHP_RUN) composer run tests

## [Composer] Run continuous integration suite (tests, code style checks, static analysis)
ci:
	$(PHP_RUN) composer run ci

## [Doctrine] Generate migration by comparing current database to your mapping information
db-diff:
	$(PHP_RUN) bin/console doctrine:migrations:diff -n

## [Doctrine] Migrate database
db-migrate:
	$(PHP_RUN) bin/console doctrine:migrations:migrate -n

## [Doctrine] Validate database schema
db-validate:
	$(PHP_RUN) bin/console doctrine:schema:validate

## [Translations] Extract translations
translation-extract:
	docker-compose exec php bin/console translation:extract --force --prefix="" en

## [Translations] Push translations
translation-push:
	docker-compose exec php bin/console translation:push --force crowdin --locales=en --domains=messages

## [Translations] Pull translations
translation-pull:
	docker-compose exec php bin/console translation:pull --force crowdin --locales=es-ES --locales=fr --locales=pt-PT --locales=en --domains=messages
	mv translations/messages.es-ES.xlf translations/messages.es.xlf
	mv translations/messages.pt-PT.xlf translations/messages.pt.xlf

## [Redis] Redis flush all
redis-flushall:
	docker-compose exec php bin/console redis:flushall -n

## [Redis] Redis flush session client
redis_flush_sessions:
	docker-compose exec php bin/console redis:flushdb -n --client=session

## [Redis] Redis flush cache
redis_flush_cache:
	docker-compose exec php bin/console redis:flushdb -n --client=default
