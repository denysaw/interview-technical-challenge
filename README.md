# Technical Interview Challenge
The task is [here](https://s3.eu-central-1.wasabisys.com/denysaw/Take_Home_-_Backend_Parking_Lot_Challenge.pdf)

## Installation

1. Install docker compose https://docs.docker.com/compose/install/#scenario-one-install-docker-desktop
2. Clone the repository
2. Run `docker compose up`
4. Run `docker compose exec my-app composer install -o`
3. Run `docker compose exec my-app php artisan migrate --seed`
4. Load in browser http://localhost:8081

## Testing
1. Migrate and seed a test database: `docker compose exec my-app php artisan migrate --seed --env=testing`
2. Run tests with `docker compose exec my-app php artisan test`
