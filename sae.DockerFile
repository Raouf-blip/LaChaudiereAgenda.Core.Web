FROM php:8.2-apache

# dl les dépendances mysql
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev mariadb-client \
    && docker-php-ext-install pdo pdo_mysql

# Copier le code source dans le container
COPY ./api/public /var/www/html
WORKDIR /var/www/html

# dl composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer install

EXPOSE 80
