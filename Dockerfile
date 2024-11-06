FROM node:22.11.0-alpine3.20 as build-stage

WORKDIR /app
COPY package*.json ./

RUN yarn

COPY . .

RUN yarn build

FROM php:8.2.25-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
    vim \
    locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer


RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite
RUN pecl install apcu && docker-php-ext-enable apcu

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www

RUN composer install
COPY --from=build-stage /app/public/build public/build

COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN service cron start

EXPOSE 80
EXPOSE 443