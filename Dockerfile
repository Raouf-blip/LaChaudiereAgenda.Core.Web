FROM php:8.4-cli

RUN apt-get update && \
    apt-get install --yes --force-yes \
    cron openssl

RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions gettext iconv intl tidy zip sockets \
    && install-php-extensions pgsql mysqli \
    && install-php-extensions pdo_mysql pdo_pgsql \
    && install-php-extensions @composer

WORKDIR /app
COPY . /app

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
