FROM php:8.2-fpm
MAINTAINER evi_tut@mail.ru

ARG USER
ARG USER_ID
ARG GROUP_ID
ARG SSH_PASS

WORKDIR /var/www

RUN apt-get update &&  \
    apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    vim \
    libicu-dev \
    supervisor \
    cron \
    libgmp-dev \
    net-tools\
    telnet \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash \
    && apt-get install nodejs -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql intl

RUN pecl install xdebug \
    && pecl install redis \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable redis

COPY xdebug.ini "${PHP_INI_DIR}/conf.d"

RUN groupadd --force -g $GROUP_ID $USER
RUN useradd -ms /bin/bash --no-user-group -g $GROUP_ID -u 1337 $USER
RUN usermod -u $USER_ID $USER

# SSH
RUN apt-get install -y openssh-server
RUN mkdir -p /var/run/sshd
RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config
RUN echo "$USER:${SSH_PASS}" |chpasswd
RUN apt-get install -y sudo \
  && echo "$USER ALL=(ALL) NOPASSWD: /usr/sbin/sshd -D" >> /etc/sudoers

# Supervisor
COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY start.sh /start.sh
RUN sed -i "s/\$_USER/$USER/g" /etc/supervisor/supervisord.conf

# Создаем файлы логов
RUN mkdir -p /var/www/cron \
    && touch /var/www/cron/cron.log \
    && chown $USER:$USER /var/www/cron/cron.log \
    && touch /var/run/crond.pid \
    && chown $USER:$USER /var/run/crond.pid

RUN mkdir /var/log/cron -p /var/log/php-fpm -p \
    && touch /var/log/xdebug.log /var/log/cron/out.log /var/log/php-fpm/out.log /var/log/cron/error.log /var/log/php-fpm/error.log \
    && chmod 777 /var/log/xdebug.log /var/log/cron/out.log /var/log/cron/error.log /var/log/php-fpm/out.log /var/log/php-fpm/error.log

# Allow user to run crontab
RUN echo "$USER ALL=(ALL) NOPASSWD: /usr/sbin/cron" >> /etc/sudoers
RUN echo "$USER ALL=(ALL) NOPASSWD: /usr/sbin/service cron restart" >> /etc/sudoers
RUN echo "$USER ALL=(ALL) NOPASSWD: /usr/sbin/service cron status" >> /etc/sudoers
RUN chmod gu+rw /var/run \
   && chmod gu+s /usr/sbin/cron

USER $USER

CMD ["bash", "/start.sh"]
