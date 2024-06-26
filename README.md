# Custom Slim Skeleton

## Готовим проект

1. Создаем проект
2. В корне проекта создаем папку app
3. Переходим в папку app, устанaвливаем
   сабмодуль `git submodule add https://github.com/stereo-type/slim-skeleton-core.git Core`
4. Копируем из папки [app/Core/_copy](/app/Core/_copy) все файлы в корень проекта
5. Переходим в папку докера [/_docker](/_docker). Создаем файл .env, копируем в него
   содержимое [.env.example](/_docker/.env.example) и заполняем данными
6. В корне проекта создаем файл .env и копируем в него содержимое [.env.example](/.env.example)
7. Заполняем файл окружения необходимыми параметрами
8. В корне основного проекта создаем папки storage,public,migrations

9. Запускаем контейнеры командой `docker-compose up -d` из папки [/_docker](/_docker)
10. Входим в контейнер командой `docker exec -it slim-skeleton-app bash`. Дальнейшие команды будем выполнять из
    контейнера
11. Устанавливаем зависимости PHP командой `composer install`
12. Устанавливаем зависимости JS командой `npm i`
13. Сгенерировать ключ приложения командой `php cli app:generate-key` и прописать в .env APP_KEY=
14. Подключиться к базе данных прописанной в .env и создать БД

## Добавляем в проект директории конфигов

1. В корне проекта создаем папку _configs
2. Добавляем в папку __configs файл config.php. В нем указываются дополнительные конфиги
    ```php
    <?php

    declare(strict_types=1);

    $def_config = require CORE_CONFIG_PATH.'/core_config.php';

    $config = [
    /**Дополнить своими конфигами*/
    ];

    return $def_config + $config;
    ```
3. Добавляем в папку __configs файл container_bindings.php. Вызывать ничего не надо, вызовется автоматически из
   app/Core/_configs/container/container.php
    ```php
   <?php
   
    declare(strict_types=1);
    
    return [
    
    ];
   ```
4. Добавляем папку __configs файл middleware.php
   ```php
   <?php

   declare(strict_types=1);
   
   use Slim\App;
   
   return static function (App $app) {
   $core_middleware = require CORE_CONFIG_PATH.'/middleware.php';
   $core_middleware($app);
   /**Добавлять тут свои middleware*/
   };

   ```
5. Добавляем в папку __configs файл routes.php
   ```php
   <?php
   
   declare(strict_types=1);
      
   use Slim\App;
   
   return static function (App $app) {
       $core_router = require CORE_CONFIG_PATH.'/routes.php';
       $core_router($app);
   };

   ```

## Добавляем в проект директории aссетов

1. В корне проекта добавляем папку _assets
2. В папке могут находиться шаблоны twig - templates
3. В папке могут находиться скрипты JS и TS - js
4. В папке могут находиться картинки images
5. В папке могут находиться шрифты fonts
6. В папке могут находиться стили css и scss

## Билд

1. Запустить сборку файлов стилей/шаблонов командой `npm run dev`
2. Создаем миграции командной `php cli migrations:diff`
3. Выполняем миграции командной `php cli migrations:migrate`
4. Создаем список предустановленных ролей `php cli app:generate-roles`
5. Создаем пользователя через регистрацию, перехватываем письмо через mailhog, варифицируем учетную запись

## Проекте подключен Bootstrap 5

Для инициализации использовать скрипт [app/Core/_assets/js/app.ts](/app/Core/_assets/js/app.ts) или
шаблон [app/Core/_assets/templates/layout.twig](/app/Core/_assets/templates/layout.twig)

## Маршрутизация

1. В проекте используется маршрутизация Slim php
2. Файл `/_configs/routes.php` предназначен для подключения базовых маршрутов - авторизация/профайл/логаут/главная и тд
3. Все маршруты из папки app находящиеся на любом уровне вложенности в формате `_configs/routes.php` будут добавлены в
   маршрутную карту.
4. Таким образом если основной проект будет расположен в папке app, то достаточно описать маршруты, одключать из не надо
5. `/_configs/routes.php` предназначен для подключения маршрутов выходящих за рамки /app

## Middleware

1. В проекте используется middleware Slim php
2. Файл `/_configs/middleware.php` предназначен для подключения middleware подключенных ко всему приложению



### ПОЛЕЗНЫЕ КОМАНДЫ ###

1. `docker stop $(docker ps -a -q) && docker rm $(docker ps -a -q) && docker system prune -a && docker rmi $(docker images -a -q)`
2. `cd /home/USER/projects/testproject/docker && docker-compose down && docker-compose up -d --build && chmod -R 777
   /home/USER/projects/testproject/dockerstorage`
3. `docker exec -it testproject-app /bin/bash`
4. `ssh -R 4510:127.0.0.1:9003 -p 5022 USER@192.168.1.101`
5. `crontab -l` покажет список текущих крон-задач для вашего пользователя.
6. `crontab -u root -l` # Просмотр существующих заданий (если они есть)
7. `crontab -u root -e` # Редактирование крон-файла для пользователя root
8. `ps aux | grep cron` активные процессы крона в системе
9. `service cron status`/`service cron restart`
10. `sudo lsof -i :80` проверка порта
11. `sudo du -h --max-depth=1 /var/lib | sort -rh` узнать объем папок

