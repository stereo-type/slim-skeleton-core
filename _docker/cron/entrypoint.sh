#!/bin/bash

# Изменяем владельца и группу файлов cron
chown -R root:root /etc/cron.d

# Запускаем оригинальную команду
exec "$@"
