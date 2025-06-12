FROM php:8.3-fpm

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Préparation du dossier de travail
WORKDIR /var/www

# Copier le code et installer les dépendances
COPY . /var/www
RUN composer install --no-dev --optimize-autoloader

# Permissions (si nécessaire)
RUN chown -R www-data:www-data /var/www
