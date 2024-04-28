#!/bin/bash
if [[ ! -z ${ISCRON} ]];
then
    echo "Is cron!"
#    cp /cronjob /etc/cron.d/cronjob
    chmod 0644 /etc/cron.d/crontab
    chmod +x /etc/cron.d/crontab
    crontab /etc/cron.d/crontab
else
    echo "Is not cron!"
fi

mkdir /var/log/cron -p
touch /var/log/cron/out.log
touch /var/log/cron/error.log
chmod 777 /var/log/cron/out.log /var/log/cron/error.log

mkdir /var/log/php-fpm -p
touch /var/log/php-fpm/out.log
touch /var/log/php-fpm/error.log
chmod 777 /var/log/php-fpm/out.log /var/log/php-fpm/error.log

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
