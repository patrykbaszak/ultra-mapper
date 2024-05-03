FROM php:latest

RUN apt update \
    && apt-get update \
    && apt install -y git jq unzip

## install xdebug extension
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

## install opcache extension
# RUN pecl install opcache \
#     && docker-php-ext-enable opcache

## install amqp extension
# RUN apt-get install -y librabbitmq-dev \
#     && pecl install amqp \
#     && docker-php-ext-enable amqp

## install redis extension
# RUN pecl install redis \
#     && docker-php-ext-enable redis

## install composer
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN mkdir -p /.composer && chmod -R 777 /.composer

### Setup environment variables
COPY package.json composer.json .gi[t] /tmp/
WORKDIR /tmp
## define app version and store it in environment variable
RUN echo "APP_VERSION=$(jq -r '.version' '/tmp/package.json')" >> /etc/environment
RUN rm -rf /tmp/package.json
## define app commit sha and store it in environment variable
RUN echo "APP_COMMIT_SHA=$(git rev-parse HEAD)" >> /etc/environment
RUN echo "APP_COMMIT_SHA_SHORT=$(git rev-parse --short HEAD)" >> /etc/environment
RUN rm -rf .git
## define app name and store it in environment variable
RUN echo "APP_NAME=$(jq -r '.name' '/tmp/composer.json')" >> /etc/environment
RUN echo "APP_DESCRIPTION=\"$(jq -r '.description' '/tmp/composer.json')\"" >> /etc/environment
RUN echo "APP_TITLE=\"$(jq -r '.title' '/tmp/composer.json')\"" >> /etc/environment
RUN rm -rf /tmp/composer.json
WORKDIR /app
### End of environment variables setup
