FROM php:8.2-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync \
    && install-php-extensions \
    pspell \
    intl \
    imap \
    opcache \
    gd \
    xdebug \
    zip \
    imagick \
    bcmath \
    bz2 \
    exif \
    gettext \
    mcrypt \
    msgpack \
    mysqli \
    pdo_mysql \
    yaml \
    @composer \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && apt-get update && apt-get install -y ssh git unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt install symfony-cli

COPY ./source /app

WORKDIR /app

RUN git config --global user.email "ushop@horoshop.com" \
    && git config --global user.name "Horoshop User" \
    && git config --global --add safe.directory /app
