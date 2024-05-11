<?php
/**
 * @package  trade Times.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Lib;

use App\Features\Enum\Format;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

class Times
{
    public static function monthBegin(int $timestamp = 0, Format $format = Format::DATETIME): string|int|DateTime
    {
        if ($timestamp === 0) {
            $timestamp = time();
        }
        return self::modifyDate(date('Y-m-01 00:00:00', $timestamp), $format);
    }

    public static function monthEnd(int $timestamp = 0, Format $format = Format::DATETIME): string|int|DateTime
    {
        if ($timestamp === 0) {
            $timestamp = time();
        }
        return self::modifyDate(date('Y-m-t 23:59:59', $timestamp), $format);
    }


    public static function buildDate(mixed $date): DateTime
    {
        if ($date instanceof DateTime) {
            return clone $date;
        }

        if (is_string($date)) {
            $time = strtotime($date);
        } elseif (is_numeric($date)) {
            $time = (int)$date;
        } else {
            throw new InvalidArgumentException('Invalid date format');
        }

        return (new DateTime())->setTimestamp($time);
    }


    public static function modifyDate(string $time, Format $format): string|int|DateTime
    {
        return match ($format) {
            Format::DATETIME => (new DateTime())->setTimestamp(strtotime($time)),
            Format::INT => strtotime($time),
            Format::STRING => date('d.m.Y H:i', strtotime($time)),
            default => throw new InvalidArgumentException("Не правильный формат " . $format->value),
        };
    }

    public static function intervalSeconds(DateInterval $interval): int
    {
        $reference = new DateTimeImmutable();
        $endTime = $reference->add($interval);
        return $endTime->getTimestamp() - $reference->getTimestamp();
    }


}
