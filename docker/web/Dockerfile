FROM banderson/symfony:base

ENV APP_ENV="prod"

COPY docker/web/config/php.ini          /usr/local/etc/php/php.ini
COPY docker/web/config/000-default.conf /etc/apache2/sites-available/000-default.conf

# Symfony project structure.
COPY symfony/bin              /var/www/symfony/bin
COPY symfony/config           /var/www/symfony/config
COPY symfony/src              /var/www/symfony/src
COPY symfony/templates        /var/www/symfony/templates
COPY symfony/migrations       /var/www/symfony/migrations
COPY symfony/composer.json    /var/www/symfony/composer.json
COPY symfony/composer.lock    /var/www/symfony/composer.lock
COPY symfony/public/css       /var/www/symfony/public/css
COPY symfony/public/index.php /var/www/symfony/public/index.php
COPY symfony/.env             /var/www/symfony/.env

RUN mkdir -p /var/www/symfony/var \
	&& composer install -d /var/www/symfony \
	&& chown -R www-data:www-data /var/www/symfony

COPY docker/web/entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80

CMD ["apache2-foreground"]
