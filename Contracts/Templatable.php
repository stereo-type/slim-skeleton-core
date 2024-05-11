<?php
/**
 * @package  trade Rendarable.php
 * @copyright 11.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Contracts;

use stdClass;

interface Templatable
{
    public function export_for_template(): stdClass;
}
