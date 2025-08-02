FROM php:7.4-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    default-mysql-client \
    && docker-php-ext-install zip mysqli pdo pdo_mysql \
    && a2enmod rewrite

# Set Virtual Host
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

