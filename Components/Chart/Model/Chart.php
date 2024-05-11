<?php
/**
 * @package  trade ChartObject.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model;

use App\Core\Components\Chart\Model\Collection\Datasets;
use App\Core\Components\Chart\Model\Collection\Labels;
use App\Core\Components\Chart\Model\Collection\Options;

readonly class Chart
{
    public function __construct(
        private Library $library,
        private ChartType|ApexType $type,
        private string $header,
        private Labels $labels,
        private Datasets $dataSets,
        private Options $options,
    ) {
    }

    public static function build(
        Library $library,
        ChartType|ApexType $type,
        string $header,
        iterable $labels,
        iterable $dataSets,
        iterable $options = []
    ): Chart {
        $l = Labels::fromArray($labels);
        $ds = Datasets::fromArray($dataSets);
        $o = Options::fromArray($options);
        return (new Chart(library: $library, type: $type, header: $header, labels: $l, dataSets: $ds, options: $o));
    }

    public function toMap(): array
    {
        return [
            'library'  => $this->library->value,
            'type'     => $this->type->value,
            'labels'   => $this->labels->toArray(),
            'datasets' => $this->dataSets->toArray(),
            'options'  => $this->options->toArray(),
            'header'   => $this->header,
        ];
    }

}
