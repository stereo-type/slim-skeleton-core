<?php
/**
 * @package  trade ChartType.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model;

enum ApexType: string
{
    case line = 'line';
    case area = 'area';
    case bar = 'bar';
    case candlestick = 'candlestick';
    case boxPlot = 'boxPlot';
    case rangeBar = 'rangeBar';
    case rangeArea = 'rangeArea';
    case bubble = 'bubble';
    case scatter = 'scatter';
    case heatmap = 'heatmap';
    case treemap = 'treemap';
    case pie = 'pie';
    case polarArea = 'polarArea';
    case donut = 'donut';
    case radar = 'radar';
    case radialBar = 'radialBar';
}