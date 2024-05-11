<?php
/**
 * @package  trade Format.php
 * @copyright 05.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Enum;

enum Format: string
{
    case INT = 'int';
    case FLOAT = 'float';
    case STRING = 'string';
    case DATETIME = 'datetime';

}
