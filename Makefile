.PHONY: up down restart logs ps bash artisan composer test pint tinker

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

build:
	docker compose up -d --build

logs:
	docker compose logs -f

logs-app:
	docker compose logs -f app

logs-vite:
	docker compose logs -f vite

ps:
	docker compose ps

bash:
	docker compose exec -it app bash

artisan:
	docker compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

composer:
	docker compose exec app composer $(filter-out $@,$(MAKECMDGOALS))

npm:
	docker compose exec vite npm $(filter-out $@,$(MAKECMDGOALS))

test:
	docker compose exec app php artisan test --compact

pint:
	docker compose exec app vendor/bin/pint

tinker:
	docker compose exec -it app php artisan tinker

# Allow arbitrary arguments to artisan/composer
%:
	@:
