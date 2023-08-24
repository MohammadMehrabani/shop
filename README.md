<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
</p>

## setup steps

- `cp .env.example .env`
- `openssl rand -base64 756 > ./docker/8.2/key`
- `sudo chmod 400 ./docker/8.2/key`
- `sudo chmod +x ./docker/8.2/rs-init.sh`
- `sudo chmod +x ./docker/8.2/createUser.sh`
- `sudo docker compose up -d`
- `sudo docker exec mongo_primary /scripts/rs-init.sh`
- `sudo docker exec mongo_primary /scripts/createUser.sh`
- `sudo docker exec app composer install`
- `sudo ./vendor/bin/sail artisan key:generate`
- `sudo ./vendor/bin/sail artisan jwt:secret`
- `sudo ./vendor/bin/sail artisan migrate --seed`
- `sudo ./vendor/bin/sail artisan test`

## base url api 
http://localhost:8080/api

## endpoint for test
http://localhost:8080/api/products

## admin user:

email: `mehraban.dev@gmail.com`

password: `123456`

## postman documentation
https://documenter.getpostman.com/view/16995623/2s9Xy5NqsU
