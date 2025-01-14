SHELL := /bin/bash

tests:
	symfony console doctrine:database:drop --force --env=test	
	symfony console doctrine:database:create --env=test
	symfony console doctrine:migrations:migrate -n --env=test
	symfony console doctrine:fixtures:load -n --env=test
	php bin/phpunit tests
	symfony php bin/phpunit $(MAKECMDGOALS)
.PHONY: tests