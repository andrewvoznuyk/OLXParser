FROM php:8.2-fpm-alpine

WORKDIR /var/www/app

RUN apk update && apk add \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libsocket++0 libsocket++-dev \
    bash \
    && docker-php-ext-install pdo pdo_mysql \
    && apk --no-cache add nodejs npm

RUN docker-php-ext-install sockets bcmath

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

USER root
RUN chmod 777 -R /var/www/app