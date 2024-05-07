<?php

namespace App\Core\Lib;

class Numbers
{

    public const DECIMAL_DELIMITER = 100000000;
    public const DECIMAL_LENGTH = 8;

    public static function split_float(string|float $floatVal): array
    {
        $floatVal = (string)($floatVal ?: '0.0');
        /**Округляем до DECIMAL_LENGTH знаков*/
        $floatVal = (string)round((float)$floatVal, self::DECIMAL_LENGTH);
        [$wholePart, $fractionPart] = array_pad(explode('.', $floatVal), 2, 0);
        /**Добиваем дробную часть до DECIMAL_LENGTH знаков*/
        $fractionPart = str_pad((string)$fractionPart, self::DECIMAL_LENGTH, '0');
        return [(int)$wholePart, (int)$fractionPart];
    }

    public static function combine_float(?int $integer, ?int $decimal): string
    {
        $integer = $integer ?? 0;
        $decimal = $decimal ?? 0;
        $fractionPart = number_format($decimal / self::DECIMAL_DELIMITER, self::DECIMAL_LENGTH, '.', '');
        return $integer . '.' . str_replace(
                '0.',
                '',
                str_pad($fractionPart, self::DECIMAL_LENGTH, 0)
            );
    }
}