<?php
/**
 * @package  trade Option.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model;

class Option
{
    public function __construct(
        public string $key,
        public mixed $value
    ) {
    }

    public function __toString(): string
    {
        $value = trim((string)$this->value);
        return "$this->key = \"$value\"";
    }

    public function toArray(): array
    {
        return  [
            $this->key => $this->value,
        ];
    }

}
