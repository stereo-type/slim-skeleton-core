<?php
/**
 * @package  trade Arrays.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Lib;

use InvalidArgumentException;

class Arrays
{
    public const REDUCE_MAX = 'max';
    public const REDUCE_MIN = 'min';
    public const REDUCE_AVERAGE = 'average';


    public static function reduce(array $array, string|callable $field, string $type): float
    {
        if (empty($array)) {
            return 0.0;
        }
        $items = array_map(static function ($item) use ($field) {
            $value = null;
            if (is_string($field)) {
                if (is_array($item)) {
                    $value = $item[$field];
                } elseif (is_object($item)) {
                    $getter = 'get' . ucfirst($field);
                    if (method_exists($item, $getter)) {
                        $value = $item->$getter();
                    }
                }
            } else {
                $value = (float)$field($item);
            }
            return $value ?? 0.0;
        }, $array);


        if ($type === self::REDUCE_AVERAGE) {
            return array_sum($items) / count($items);
        }

        return match ($type) {
            self::REDUCE_MAX => max($items),
            self::REDUCE_MIN => min($items),
            default => throw new InvalidArgumentException('Wrong type: ' . $type),
        };
    }

}
