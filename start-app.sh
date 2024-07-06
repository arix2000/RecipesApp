#!/bin/sh
echo -e "\033[33m\n [WARNING] Before running this script make sure that docker system service is running and npm is installed\033[0m"
npm install
npm run dev
docker compose down
docker compose up -d --build
docker compose exec app composer install
docker compose exec app chown -R www-data:www-data /var/www/html/public/recipe/uploads
docker compose exec app chmod -R 775 /var/www/html/public/recipe/uploads
docker compose exec app php bin/console doctrine:schema:drop --force --no-interaction > /dev/null 2>&1
docker compose exec app php bin/console doctrine:migrations:version --delete --all --no-interaction > /dev/null 2>&1
docker compose exec app php bin/console doctrine:schema:create --no-interaction > /dev/null 2>&1
docker compose exec app php bin/console make:migration --no-interaction
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
echo -e "\n [NOTE] Loading fixtures... it may take a while."
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
echo -e "\n\nvisit to see beautiful app: http://localhost/  | or http://0.0.0.0/  for some browsers"
echo -e "visit to see api docs: http://localhost/api/doc  | or http://0.0.0.0/api/doc  for some browsers"
echo -e "\033[33m\n [WARNING] the initial images are based on https://foodish-api.com/api/, if the images do not load, \n    it may be because the api is not responding which happens quite often. \n    After a while everything usually goes back to normal.\033[0m"
echo -e "\033[0;32m\n [TIP] Run: \`docker compose exec app php bin/console app:add-admin\` to add admin\033[0m"
echo -e "\033[0;32m [TIP] Run: \`docker compose exec app php bin/console app:change-user-password\` to change password of a single user\033[0m"


