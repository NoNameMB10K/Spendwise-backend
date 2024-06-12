FROM php:8.3-fpm

WORKDIR /srv/app/

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y git zip libpq-dev libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo_mysql pdo opcache

RUN curl -sL https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer

RUN apt-get update \
    && apt-get -y install nginx

COPY --from=docker --link ./docker-entrypoint.sh /
COPY --from=docker --link ./default.conf /etc/nginx/sites-available/default

RUN chmod +x /docker-entrypoint.sh

RUN usermod --uid 1000 www-data
RUN groupmod --gid 1000 www-data

CMD ["/bin/bash", "-c", "/docker-entrypoint.sh"]
