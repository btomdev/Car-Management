# Variables
DOCKER = docker
DOCKER_COMPOSE_BIN ?= docker-compose
DOCKER_COMPOSE     = DOCKER_UID=$(shell id -u) DOCKER_GID=$(shell id -g $(whoami)) $(DOCKER_COMPOSE_BIN)
EXEC = $(DOCKER) exec -it php-fpm
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
NPM = $(EXEC) npm
SYMFONY_CONSOLE = $(PHP) bin/console

# Colors
GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

## —— 🔥 App —————————————————————————————————————————————————————————————————————————
start: ## Start app
	$(MAKE) create-env
	$(MAKE) create-folders
	$(MAKE) build
	$(MAKE) up
	$(MAKE) composer-install
	$(MAKE) npm-install
	$(MAKE) database-init
	@echo
	@echo "📣 L'app' est disponible !"
	@echo
	@echo "✅ app : \033[32mhttp://localhost:80/home\033[0m"
	@echo "✅ database : \033[32mhttp://localhost:8081\033[0m"
	@echo "✅ maildev : \033[32mhttp://localhost:1080/\033[0m"

cache-clear: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear

shell:
	$(EXEC) /bin/bash

console:
	$(PHP) ./bin/console $(COMMAND)

create-env:
	cp .env.dist .env

## —— ✅ Test ————————————————————————————————————————————————————————————————————————
.PHONY: tests
tests: ## Run all tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/
	$(PHP) bin/phpunit --testdox tests/Functional/
	$(PHP) bin/phpunit --testdox tests/E2E/

database-init-test: ## Init database for test
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test

unit-test: ## Run unit tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/

functional-test: ## Run functional tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Functional/

# PANTHER_NO_HEADLESS=1 ./bin/phpunit --filter LikeTest --debug to debug with Chrome
e2e-test: ## Run E2E tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/E2E/

## —— 🐳 Docker ——————————————————————————————————————————————————————————————————————
build: ## Build app with fresh images
	$(DOCKER_COMPOSE) build
	$(DOCKER) image prune -f

up:
	$(DOCKER_COMPOSE) up -d

stop: ## Stop app
	$(DOCKER_COMPOSE) stop

down:
	$(DOCKER_COMPOSE) down

clean-up: ## Remove containers, images and volumes
	$(DOCKER_COMPOSE) down --remove-orphans --rmi all --volumes

## —— 🎻 Composer ————————————————————————————————————————————————————————————————————
composer-install: ## Install dependencies
	$(COMPOSER) install

composer-update: ## Update dependencies
	$(COMPOSER) update

composer-clear-cache: ## clear-cache dependencies
	$(COMPOSER) clear-cache

## —— 🐈 NPM —————————————————————————————————————————————————————————————————————————
npm-install: ## Install all npm dependencies
	$(NPM) init -y
	$(NPM) install

npm-update: ## Update all npm dependencies
	$(NPM) update

npm-watch: ## Update all npm dependencies
	$(NPM) run watch

## —— 📊 Database ————————————————————————————————————————————————————————————————————
database-init: ## Init database
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate
	$(MAKE) database-fixtures-load

database-drop: ## Create database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-create: ## Create database
	$(SYMFONY_CONSOLE) d:d:c --if-not-exists

database-remove: ## Drop database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-migration: ## Make migration
	$(SYMFONY_CONSOLE) make:migration

database-migrate: ## Migrate migrations
	$(SYMFONY_CONSOLE) d:m:m --no-interaction

database-fixtures-load: ## Load fixtures
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

create-folders: ## Create ignored folders
	mkdir -p ./data/postgres ./logs/nginx

factory: ## Create a persistent object factory
	$(SYMFONY_CONSOLE) make:factory

## —— 🧽 Linters ————————————————————————————————————————————————————————————————————
lint-all: ## Launch all linters
	$(MAKE) lint-container
	$(MAKE) lint-yaml

lint-controler: ## Ensure the container is configured correctly
	$(SYMFONY_CONSOLE) lint:container

lint-yaml: ## Ensure the container is configured correctly
	$(SYMFONY_CONSOLE) lint:yaml

## —— 🛠️  Others ————————————————————————————————————————————————————————————————————
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'