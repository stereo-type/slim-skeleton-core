<?php
/**
 * @package  trade ChartType.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model;

enum ChartType: string
{
    case bar = 'bar';
    case line = 'line';
    case pie = 'pie';
    case doughnut = 'doughnut';
    case bubble = 'bubble';
    case polarArea = 'polarArea';
    case scatter = 'scatter';
    case radar = 'radar';
}