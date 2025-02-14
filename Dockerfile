FROM php:8.2-fpm

ARG USER_ID
ARG GROUP_ID

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
    sed -i -E 's/^(UID_MAX\s+)[0-9]{1,}$/\1600000000/g' /etc/login.defs &&\
    sed -i -E 's/^(GID_MAX\s+)[0-9]{1,}$/\1600000000/g' /etc/login.defs &&\
    userdel -f www-data &&\
    if getent group www-data ; then groupdel www-data; fi &&\
    groupadd -g ${GROUP_ID} www-data &&\
    useradd -l -u ${USER_ID} -g www-data www-data &&\
    install -d -m 0755 -o www-data -g www-data /home/www-data &&\
    chown --changes --silent --no-dereference --recursive \
          --from=33:33 ${USER_ID}:${GROUP_ID} \
        /home/www-data \
;fi

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin

RUN chmod +x /usr/local/bin/install-php-extensions  && sync \
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
    && apt-get update && apt-get install -y ssh git unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt install symfony-cli

USER www-data

COPY ./source /app

WORKDIR /app

RUN git config --global user.email "ushop@horoshop.com" \
    && git config --global user.name "Horoshop User" \
    && git config --global --add safe.directory /app