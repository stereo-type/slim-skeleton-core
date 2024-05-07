<?php

declare(strict_types = 1);

use App\Core\Components\Admin\Controllers\AdminController;
use App\Core\Controllers\UserCatalogController;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\VerifyEmailMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) {


    $app->group('/admin', function (RouteCollectorProxy $group) use ($app) {
        $group->get('', [AdminController::class, 'index'])->setName('admin');
//        $group->get('/tes', [AdminController::class, 'tes'])->setName('tes');
        UserCatalogController::routing($app, '/admin/user');
    })->add(VerifyEmailMiddleware::class)
//        ->add(RoleMiddleware::admin())
        ->add(AuthMiddleware::class);
};
