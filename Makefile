up: docker-up
init: docker-down-clear docker-pull docker-build docker-up
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