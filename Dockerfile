FROM php:5.4.45-apache

COPY ./ /var/www/
COPY config/php.ini /usr/local/etc/php/
COPY config/apache2.conf /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng12-dev
RUN docker-php-ext-install mcrypt
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install zip
RUN a2enmod rewrite
RUN apache2ctl restart

RUN rm -r /var/www/html
WORKDIR /var/www

EXPOSE 80