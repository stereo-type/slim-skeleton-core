<?php
/**
 * @package  trade DataSet.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model;

use App\Core\Components\Chart\Model\Collection\Options;

readonly class DataSet
{
    public function __construct(
        public string $label,
        public array $data,
        public Options $options,
    ) {
    }

    public static function fromArray(iterable $array): DataSet
    {
        if ($array instanceof self) {
            return $array;
        }

        return new DataSet(
            label: $array['label'],
            data: $array['data'],
            options: Options::fromArray($array['options'] ?? [])
        );
    }

    public function toArray(): array
    {
        return [
            'label'   => $this->label,
            /**Дублируем для другой библиотеки*/
            'name'   => $this->label,
            'data'    => $this->data,
            'options' => $this->options->toArray(),
        ];
    }

}