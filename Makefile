# Variables
DOCKER = docker 
DOCKER_COMPOSE = docker-compose
EXEC = ${DOCKER} exec -w /var/www/project www_symfony_blog
PHP = ${EXEC} php
COMPOSER = ${EXEC} composer
NPM = ${EXEC} npm
SYMFONY_CONSOLE = ${PHP} bin/console

# Colors
GREEN =/bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

## —— 🔥 App ——
init: ## Lancer le projet / Init the project
	$(MAKE) start
	$(MAKE) composer-install
	$(MAKE) npm-install
	@$(call GREEN,"The application is available at: http://127.0.0.1:8000/.")

cache-clear: ## Effacer le cache / Clear cache
	$(SYMFONY_CONSOLE) cache:clear


## —— 🐳 Docker ——
start: ## Démarrer les container / Start app
	$(MAKE) docker-start 
docker-start: 
	$(DOCKER_COMPOSE) up -d

stop: ## Arrêter les containers / Stop app
	$(MAKE) docker-stop
docker-stop: 
	$(DOCKER_COMPOSE) stop
	@$(call RED,"The containers are now stopped.")

stopAll: ## Arrêter tout les containers en cours
	$(DOCKER) stop $$(docker ps -a -q)

## —— ✅ Test ——
.PHONY: tests
tests: ## Exécuter tous les tests / Run all tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/
	$(PHP) bin/phpunit --testdox tests/Functional/
	$(PHP) bin/phpunit --testdox tests/E2E/

database-init-test: ## Lancer la base de données pour le test / Init database for test
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test

unit-test: ## Exécuter des tests unitaires / Run unit tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/

functional-test: ## Exécuter des tests fonctionnels / Run functional tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Functional/

# PANTHER_NO_HEADLESS=1 ./bin/phpunit --filter LikeTest --debug to debug with Chrome
e2e-test: ## Exécuter des tests E2E / Run E2E tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/E2E/

## —— 🎻 Composer ——
composer-install: ## Installer les dépendances / Install dependencies
	$(COMPOSER) install

composer-update: ## Mise à jour des dépendances / Update dependencies
	$(COMPOSER) update


## —— 🐈 NPM —————————————————————————————————————————————————————————————————
npm-install: ## Installer toutes les dépendances npm / Install all npm dependencies
	$(NPM) install

npm-update: ## Mise à jour de toutes les dépendances npm / Update all npm dependencies
	$(NPM) update

npm-watch: ## Mise à jour de toutes les dépendances npm / Update all npm dependencies
	$(NPM) run watch


## —— 📊 Database ——
database-init: ## Lancer la base de données / Init database
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate
	$(MAKE) database-fixtures-load

database-create: ## Créer une base de données / Create database
	$(SYMFONY_CONSOLE) d:d:c --if-not-exists

database-drop: ## Suppression de la base de données / Database drop
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-remove: ## Abandon de la base de données / Drop database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-migration: ## Faire la migration / Make migration
	$(SYMFONY_CONSOLE) make:migration

migration: ## Alias : database-migration
	$(MAKE) database-migration

database-migrate: ## Migrer les migrations / Migrate migrations
	$(SYMFONY_CONSOLE) d:m:m --no-interaction

migrate: ## Alias : database-migrate
	$(MAKE) database-migrate

database-fixtures-load: ## Chargement des fixtures / Load fixtures
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

fixtures: ## Alias : database-fixtures-load
	$(MAKE) database-fixtures-load


## —— 🛠️  Others ——
help: ## Liste des commandes / List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
