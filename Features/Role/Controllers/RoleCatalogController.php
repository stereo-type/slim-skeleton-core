<?php
/**
 * @package  DemoCatalog.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Features\Role\Controllers;

use App\Core\Components\Catalog\Controllers\EntityCatalogController;

class RoleCatalogController extends EntityCatalogController
{
    public const USE_CACHE = true;

    public function get_name(): string
    {
        return 'Роли';
    }

    public static function get_index_route(): string
    {
        return '/admin/role/manage';
    }
}
