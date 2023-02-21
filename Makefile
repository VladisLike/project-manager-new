down: docker-down
up: docker-up
init: docker-down-clear manager-clear docker-pull docker-build docker-up run
exec_bash: docker-exec-bash

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

docker-exec-bash:
	docker exec -it crud_php-fpm bash

manager-clear:
	docker run --rm -v ${PWD}/:/app --workdir=/app alpine rm -f .ready

#Run app

run: composer-install assets-install manager-dump manager-fixture manager-ready

composer-install:
	docker exec -it crud_php-fpm composer install
assets-install:
	docker-compose run --rm node npm install
	docker-compose run --rm node npm rebuild node-sass

manager-dump:
	docker exec -it crud_php-fpm php bin/console doctrine:migrations:migrate --no-interaction

manager-fixture:
	docker exec -it crud_php-fpm php bin/console doctrine:fixtures:load --no-interaction

#manager-admin:
#	docker exec -it crud_php-fpm bin/console admin:create

manager-ready:
	docker run --rm -v ${PWD}/:/app --workdir=/app alpine touch .ready

run-dev: manager-assets-dev

manager-assets-dev:
	docker-compose run --rm node npm run dev