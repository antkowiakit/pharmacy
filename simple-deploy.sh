git pull
app/console cache:clear -e=prod
yes | app/console doctrine:migrations:migrate
app/console assets:install -e=prod
app/console assetic:dump -e=prod
chmod 777 -R app/cache
chmod 777 -R app/logs