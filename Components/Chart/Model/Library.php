<?php
/**
 * @package  trade ChartType.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model;

enum Library: string
{
    case chart = 'chart';
    case apex = 'apex';
}