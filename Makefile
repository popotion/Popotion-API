include .env.dev
.DEFAULT_GLOBAL = help
.PHONY: assets vendors

CONSOLE=symfony console
COMPOSER=symfony composer

help:		## Shows this help hint
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
	| sed 's/.*Makefile://' \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' \
	| sed -e 's/\[32m##/[33m/' \


start:		## Start project
start: config docker vendors sf-start npm

stop:		## Stop project
	symfony server:stop
	docker-compose stop

restart:	## Restart project
restart: stop start

sf-start:
	symfony server:start -d
	docker-compose up -d

##---------------------------------------------------------------------------
## Docker
##
docker:		## Start docker container
	@docker-compose up -d --remove-orphans

##---------------------------------------------------------------------------
## Dependencies
##
vendors:	## Install php dependencies
	$(COMPOSER) install

npm:		## Install front dependencies
	npm install

##---------------------------------------------------------------------------
## Cache
##
cc:			## Clear cache
	$(CONSOLE) ca:cl -e $(or $(ENV), 'dev')

##---------------------------------------------------------------------------
## Database & Data
##
db-init: 	## Init project's database
	$(CONSOLE) d:d:drop -n --force --if-exists
	$(CONSOLE) d:d:create -q

db-diff:   	## Creates doctrine migration
	$(CONSOLE) doc:mi:diff

db-migrate:	## Runs doctrine migration
	$(CONSOLE) d:m:migrate -n

db-fixtures:	## Load fixtures
	$(CONSOLE) doctrine:fixtures:load -n --append

db-reload:	## Reloads project's data
ifeq (, $(shell which symfony))
db-reload: CONSOLE=php bin/console
endif
db-reload: db-init db-migrate