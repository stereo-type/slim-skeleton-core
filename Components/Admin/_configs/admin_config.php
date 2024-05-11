<?php


use App\Core\Components\Admin\Model\Tree\AdminCategory;
use App\Core\Components\Admin\Model\Tree\AdminPage;
use App\Core\Components\Admin\Model\Tree\AdminRoot;
use App\Core\Lib\Url;
use Slim\App;

return static function (App $app, AdminRoot $root): void {
    $usersSub = [];
    $usersSub [] = new AdminCategory('accounts', 'Учетные записи', [
        new AdminPage('user', 'Список пользователей', Url::build('/admin/user'))
    ]);
    $usersSub [] = new AdminCategory('roles', 'Роли', [
        new AdminPage('manage', 'Управление ролями', Url::build('/admin/role/manage')),
        new AdminPage('assign', 'Назначение ролей', Url::build('/admin/role/assign')),
    ]);
    $users = new AdminCategory('users', 'Пользователи', $usersSub);


    $plugins = new AdminCategory('plugins', 'Плагины');


    $serverSub = [];
    $serverSub [] = new AdminCategory('environment', 'Среда');
    $serverSub [] = new AdminCategory('task', 'Задачи', [
        new AdminPage('logs', 'Журнал задач', Url::build('/admin/task/logs')),
        new AdminPage('assign', 'Назначение ролей', Url::build('/admin/task/assign')),
    ]);
    $server = new AdminCategory('server', 'Сервер', $serverSub);

    $development = new AdminCategory('development', 'Разработка');

    $root->add($root->name, $users);
    $root->add($root->name, $plugins);
    $root->add($root->name, $server);
    $root->add($root->name, $development);
};
