FROM php:8.1-fpm
LABEL maintainer="João Batista Neto <neto.joaobatista@gmail.com>"
LABEL version="1.0.0"
LABEL description="Imagem Docker para Magento 2 com módulo de pagamento da Rede"

ARG MAGENTO_REPO_USER
ARG MAGENTO_REPO_PSWD

RUN apt-get update
RUN apt-get install -y --no-install-recommends git unzip curl vim tar libfreetype6-dev libxslt1-dev zlib1g-dev libicu-dev libzip-dev libxml2-dev libonig-dev libpng-dev libjpeg-dev sudo nginx

RUN docker-php-ext-configure gd --with-jpeg --with-freetype
RUN docker-php-ext-install pdo  pdo_mysql  gd  bcmath  intl  xsl  soap  zip sockets

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
RUN echo '%www-data ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

RUN mkdir -p /var/www/.composer
RUN chown www-data.www-data /var/www/.composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

ADD ./etc /etc
ADD ./etc/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
ADD ./docker-cmd /usr/bin/docker-cmd
ADD ./wait-for-it /usr/bin/wait-for-it

RUN rm -f /var/www/html/*

USER www-data
RUN echo "{\"http-basic\": {\"repo.magento.com\": {\"username\": \"$MAGENTO_REPO_USER\", \"password\": \"$MAGENTO_REPO_PSWD\"}}}" > ~/.composer/auth.json
RUN composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=2.4.4 /var/www/html
#RUN git clone https://github.com/magento/magento2-sample-data.git /tmp/sample
#RUN php -f /tmp/sample/dev/tools/build-sample-data.php -- --ce-source=/var/www/html

USER www-data
WORKDIR "/var/www/html"
CMD ["docker-cmd", "magento"]
