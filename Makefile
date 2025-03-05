LARADOCK=laradock

# container names
PHP_CONTAINER_NAME=$(LARADOCK)-php-fpm-1
DB_CONTAINER_NAME=$(LARADOCK)-mysql-1
WORKSPACE_CONTAINER_NAME=$(LARADOCK)-workspace-1

LIST_OF_CONTAINERS_TO_RUN=nginx mysql workspace

.PHONY: initial-build
initial-build:
	cp $(LARADOCK)/.env.example $(LARADOCK)/.env
	cp .env.example .env
	cd $(LARADOCK) && docker-compose up -d $(LIST_OF_CONTAINERS_TO_RUN)
	docker exec -it $(WORKSPACE_CONTAINER_NAME) composer install
	docker exec -it $(PHP_CONTAINER_NAME) bash -c 'php artisan key:generate'
	docker exec -it $(PHP_CONTAINER_NAME) bash -c "php artisan migrate:fresh --seed"
	docker exec -it $(WORKSPACE_CONTAINER_NAME) npm install
	@echo "\n\033[92mApplication is ready http://localhost\033[0m"

# run all containers
.PHONY: up
up:
	cd $(LARADOCK) && docker-compose up -d $(LIST_OF_CONTAINERS_TO_RUN)

# stop all containers
.PHONY: down
down:
	cd $(LARADOCK) && docker-compose down

# show docker log
.PHONY: docker-log
docker-log:
	cd $(LARADOCK) && docker-compose logs -f

# JOIN containers targets

.PHONY: join-workspace
join-workspace:
	docker exec -it $(WORKSPACE_CONTAINER_NAME) bash

.PHONY: join-php
join-php:
	docker exec -it $(PHP_CONTAINER_NAME) bash

.PHONY: join-db
join-db:
	docker exec -it $(DB_CONTAINER_NAME) mysql -u default -p default

# some artisan helpers

.PHONY: key-genrate
key-generate:
	docker exec -it $(PHP_CONTAINER_NAME) bash -c 'php artisan key:generate'

.PHONY: new-migration
new-migration:
	@read -p "Migration name: " migrationname; \
	docker exec -it $(PHP_CONTAINER_NAME) bash -c "php artisan make:migration $$migrationname";

.PHONY: run-migrations
run-migrations:
	docker exec -it $(PHP_CONTAINER_NAME) bash -c "php artisan migrate"

.PHONY: run-seeds
run-seeds:
	docker exec -it $(PHP_CONTAINER_NAME) bash -c 'php artisan db:seed'

.PHONY: new
new:
	@read -p "Make command and name (e.g. event TestEvent): " commandname;\
	docker exec -it $(PHP_CONTAINER_NAME) bash -c "php artisan make:$$commandname";
#------------------

# run tests with phpunit
.PHONY: test
test:
	docker exec -it $(PHP_CONTAINER_NAME) ./vendor/bin/phpunit

# install composer dependencies
.PHONY: composer-install
composer-install:
	docker exec -it $(WORKSPACE_CONTAINER_NAME) composer install
