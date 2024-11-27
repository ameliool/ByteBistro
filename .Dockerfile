FROM php:7.4-apache

RUN docker-php-ext-install mysqli

WORKDIR /var/www/html/

COPY . .

RUN chmod -R a+r /var/www/html/
EXPOSE 80