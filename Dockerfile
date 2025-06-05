# Utilisation de l'image officielle PHP avec Apache
FROM php:8.2-apache

# Installation des dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip

# Copie du code source dans le conteneur
COPY ./api/public /var/www/html
WORKDIR /var/www/html

# Vérification si composer.json est bien présent
RUN if [ -f composer.json ]; then \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer install; \
    else echo "composer.json non trouvé, aucun install nécessaire"; \
    fi

# Expose le port 80 pour l'application
EXPOSE 80
