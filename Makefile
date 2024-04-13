# docker network create comparison_network
up:
	docker-compose up -d
composer_update:
	docker-compose run --rm php_comparison composer update
composer_install:
	docker-compose run --rm php_comparison composer install
key_gen:
	docker-compose run --rm php_comparison php artisan key:generate
migrate:
	docker-compose run --rm php_comparison php artisan migrate
seed:
	docker-compose run --rm php_comparison php artisan db:seed
queue:
	docker-compose run --rm php_comparison php artisan queue:work
down:
	docker-compose down
optimize_clear:
	docker-compose run --rm php_comparison php artisan optimize:clear
