#!/bin/sh
set -e

# Run Symfony commands
php bin/console make:migration
php bin/console doctrine:migrations:migrate --no-interaction

# Then start PHP-FPM
exec php-fpm

