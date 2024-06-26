<?php
/**
 * @package  container_bindings.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use App\Core\Features\Role\Controllers\RoleCatalogController;
use App\Core\Features\Role\RoleService\RoleCatalogDataProvider;
use App\Core\Features\User\Controllers\UserCatalogController;
use App\Core\Features\User\Services\UserCatalogDataProvider;

return [
    ...UserCatalogController::binding(UserCatalogDataProvider::class),
    ...RoleCatalogController::binding(RoleCatalogDataProvider::class),
];
