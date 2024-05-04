#!/bin/bash
if [[ ! -z ${ISCRON} ]];
then
    echo "Is cron!"
#    cp /cronjob /etc/cron.d/cronjob
    chmod 777 /etc/cron.d/crontab
    chmod +x /etc/cron.d/crontab
    crontab /etc/cron.d/crontab
else
    echo "Is not cron!"
fi


/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
