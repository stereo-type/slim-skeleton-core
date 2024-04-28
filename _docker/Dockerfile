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
    && rm -rf /var/lib/apt/lists/*

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash \
    && apt-get install nodejs -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql intl
RUN docker-php-ext-install gmp

RUN pecl install xdebug \
    && pecl install redis \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable redis

COPY xdebug.ini "${PHP_INI_DIR}/conf.d"

#RUN groupadd --force -g $GROUP_ID $USER
#RUN useradd -ms /bin/bash --no-user-group -g $GROUP_ID -u 1337 $USER
#RUN usermod -u $USER_ID $USER

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

#CMD /bin/bash -c "envsubst < /etc/supervisor/supervisord.conf.template > /etc/supervisor/supervisord.conf && supervisord -n -c /etc/supervisor/supervisord.conf"

# Set up cron log file and PID file
#RUN touch /var/run/crond.pid \
#    && chown $USER:$USER /var/run/crond.pid
#
#mkdir -p /var/www/cron \
#    && touch /var/www/cron/cron.log \
#    && chown $USER:$USER /var/www/cron/cron.log \
#    && touch /var/run/crond.pid \
#    && chown $USER:$USER /var/run/crond.pid \

# Allow user to run crontab
RUN echo "$USER ALL=(ALL) NOPASSWD: /usr/sbin/cron" >> /etc/sudoers
RUN echo "$USER ALL=(ALL) NOPASSWD: /usr/sbin/service cron restart" >> /etc/sudoers
RUN echo "$USER ALL=(ALL) NOPASSWD: /usr/sbin/service cron status" >> /etc/sudoers


USER $USER

CMD ["bash", "/start.sh"]
