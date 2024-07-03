#!/bin/sh
docker-compose down
docker-compose up -d --build
echo -e "\n [NOTE] Loading fixtures... it may take a while."
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
echo -e "\n\nvisit to see beautiful app: http://localhost/  | or http://0.0.0.0/  for some browsers"
echo -e "\033[33m\n [WARNING] the initial images are based on https://foodish-api.com/api/, if the images do not load, \n    it may be because the api is not responding which happens quite often. \n    After a while everything usually goes back to normal.\033[0m"
echo -e "\033[0;32m\n [TIP] Run: \`docker-compose exec app php bin/console app:add-admin\` to add admin\033[0m"


