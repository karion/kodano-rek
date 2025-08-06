# Ścieżki i kontenery
CONTAINER_PHP=php
CONTAINER_NODE=pwa
COMPOSE=docker compose
EXEC=$(COMPOSE) exec
SYMFONY=$(EXEC) php bin/console

.DEFAULT_GOAL := help

help: ## Wyświetl pomoc
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Uruchom kontenery w tle
	$(COMPOSE) up -d

stop: ## Zatrzymaj kontenery
	$(COMPOSE) stop

down: ## Zatrzymaj i usuń kontenery
	$(COMPOSE) down --volumes --remove-orphans

build: ## Zbuduj kontenery
	$(COMPOSE) build --pull

restart: down up ## Restart kontenerów

bash: ## Wejdź do kontenera PHP
	$(EXEC) php bash

composer-install: ## Instaluj zależności PHP
	$(EXEC) php composer install

yarn-install: ## Instaluj zależności Node.js
	$(EXEC) client yarn install

migrate: ## Wykonaj migracje Doctrine
	$(SYMFONY) doctrine:migrations:migrate

diff:
	$(SYMFONY) doctrine:migrations:diff

cache-clear: ## Wyczyść cache Symfony
	$(SYMFONY) cache:clear

log: ## Podgląd logów PHP
	$(COMPOSE) logs -f php

status: ## Pokaż status kontenerów
	$(COMPOSE) ps

reset: down build up composer-install migrate 

