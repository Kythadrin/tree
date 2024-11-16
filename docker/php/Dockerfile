FROM php:8.3-fpm

WORKDIR /var/www/tree

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    nginx \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    zip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get update && apt-get upgrade -y && apt-get install -y nodejs

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#RUN pecl install xdebug \
#    && docker-php-ext-enable xdebug

#RUN cp ./docker/php/xdebug.ini  /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

EXPOSE 9000 5173