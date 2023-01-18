#!/usr/bin/env bash

chgrp -R www-data /var/lib/php/sessions
chown -R www-data:www-data /var/www/symfony/var
chown -R www-data:www-data /var/www/symfony/public/images
chown -R www-data:www-data /var/www/symfony/public/imagine

# Wait for MySQL
while ! mysqladmin ping -h"database" --silent; do
    echo "Waiting for database connection..."
    sleep 5
done

./bin/console cache:clear
./bin/console doctrine:migrations:migrate

exec "$@"
