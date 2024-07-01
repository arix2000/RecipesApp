#!/bin/sh
docker-compose down
docker-compose up -d --build
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
echo -e "\n\nvisit to see beautiful app: http://localhost/  | or http://0.0.0.0/  for some browsers"
