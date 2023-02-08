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

run: composer-install assets-install manager-dump manager-admin manager-ready

composer-install:
	docker exec -it crud_php-fpm composer install

assets-install:
	docker-compose run --rm node npm install

manager-dump:
	docker exec -it crud_php-fpm bin/console doctrine:migrations:migrate

manager-admin:
	docker exec -it crud_php-fpm bin/console admin:create

manager-ready:
	docker run --rm -v ${PWD}/:/app --workdir=/app alpine touch .ready


run-dev: manager-assets-dev

manager-assets-dev:
	docker-compose run --rm crud_node npm run dev


