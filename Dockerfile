FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl mariadb-client libzip-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

COPY . /var/www/html
WORKDIR /var/www/html

EXPOSE 80