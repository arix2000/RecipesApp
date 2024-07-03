#!/bin/sh
set -e

php bin/console doctrine:schema:drop --force
php bin/console doctrine:migrations:version --delete --all
php bin/console doctrine:schema:create
php bin/console make:migration --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction

exec php-fpm

