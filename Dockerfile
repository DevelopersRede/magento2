FROM php:7.2-apache
LABEL maintainer="João Batista Neto <neto.joaobatista@gmail.com>"
LABEL version="1.0.0"
LABEL description="Imagem Docker para Magento 2 com módulo de pagamento da Rede"

RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y git unzip \
                           libfreetype6-dev \
                           libjpeg62-turbo-dev \
                           libxml2-dev \
                           libxslt1-dev

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
 && docker-php-ext-install pdo \
    pdo_mysql \
    gd \
    bcmath \
    intl \
    xsl \
    soap \
    zip

RUN usermod -u 1001 www-data
RUN groupmod -g 1001 www-data

RUN mkdir -p /var/www/.composer
RUN chown www-data.www-data /var/www/.composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer
RUN sed -i "13i\ \n\t<Directory \"/var/www/html\">\n\t\tAllowOverride All\n\t</Directory>" /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

USER www-data
RUN git clone https://github.com/magento/magento2.git /var/www/html
RUN git clone https://github.com/magento/magento2-sample-data.git /tmp/sample
RUN php -f /tmp/sample/dev/tools/build-sample-data.php -- --ce-source=/var/www/html

RUN composer require developersrede/erede-php

USER root