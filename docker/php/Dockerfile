FROM php:7.4-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
