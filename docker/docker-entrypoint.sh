#!/bin/bash

cd /srv/app/ || exit 1

composer install
php bin/console doctrine:migrations:migrate

service nginx start
php-fpm
