FROM php:7.2.0beta3-fpm

RUN docker-php-ext-install pdo pdo_mysql

COPY ./ /var/www
