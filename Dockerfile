
FROM mlocati/php-extension-installer:latest AS php_extension_installer

FROM php

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y git

RUN set -eux; \
    install-php-extensions \
        intl \
        zip \
        apcu \
        opcache \
    ;


VOLUME /srv/app
WORKDIR /srv/app
