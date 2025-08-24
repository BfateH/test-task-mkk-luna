FROM php:8.4.10-fpm

RUN apt-get update && apt-get install -y \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      zip unzip \
      git \
      postgresql-client \
      postgis \
      postgresql-common \
      libproj-dev \
      libgeos-dev \
      libgdal-dev \
      gdal-bin \
      && docker-php-ext-install pdo_mysql \
      && docker-php-ext-install pdo_pgsql \
      && docker-php-ext-install pdo \
      && docker-php-ext-install pgsql \
      && docker-php-ext-install bcmath \
      && docker-php-ext-install gd \
      && docker-php-ext-install zip \
      && apt-get clean \
      && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

COPY ./_docker/app/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN groupadd -g 1000 laravel \
    && useradd -u 1000 -g 1000 -m laravel \
    && usermod -aG www-data laravel
USER laravel

WORKDIR /var/www

ENTRYPOINT ["/entrypoint.sh"]
