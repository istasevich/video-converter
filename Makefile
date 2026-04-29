up:
	docker compose up --build -d

logs:
	docker compose logs -f

shell:
	docker compose exec app bash

migrate:
	docker compose exec app php artisan migrate

test:
	docker compose exec app php artisan test

queue:
	docker compose logs -f queue-video-conversions queue-video-post-processing
