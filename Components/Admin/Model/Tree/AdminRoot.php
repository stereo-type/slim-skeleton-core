<?php
/**
 * @package  trade AdminRoot.php
 * @copyright 06.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree;

use App\Core\Lib\Files;
use Slim\App;

class AdminRoot extends AdminCategory
{
    private bool $loaded;

    public function __construct()
    {
        parent::__construct('root', 'Администрирование', []);
        $this->loaded = false;
        $this->category_cache = [];
    }

    public function purge_children(): void
    {
        $this->children = array();
        $this->loaded = false;
        $this->category_cache = [];
    }


    public static function admin_get_root(App $app, $reload = false): AdminRoot
    {
        global $ADMIN;

        if (is_null($ADMIN)) {
            $ADMIN = new AdminRoot();
        }

        if ($reload) {
            $ADMIN->purge_children();
        }

        if (!$ADMIN->loaded) {
            $configs  = Files::findFiles(APP_PATH, '_configs', 'admin_config.php');
            foreach ($configs as $c) {
                $d = require $c;
                $d($app, $ADMIN);
            }
            $ADMIN->loaded = true;
        }

        return $ADMIN;
    }
}