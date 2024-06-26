<?php

declare(strict_types=1);

use App\Core\Components\Admin\Controllers\AdminController;
use App\Core\Features\Role\Controllers\RoleCatalogController;
use App\Core\Features\User\Controllers\UserCatalogController;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\VerifyEmailMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) {


    $app->group('/admin', function (RouteCollectorProxy $group) use ($app) {
        $group->get('', [AdminController::class, 'index'])->setName('admin');
        //        $group->get('/tes', [AdminController::class, 'tes'])->setName('tes');
        UserCatalogController::routing($app, '/admin/user');
        RoleCatalogController::routing($app, '/admin/role/manage');
    })->add(VerifyEmailMiddleware::class)
        ->add(AuthMiddleware::class);
};
