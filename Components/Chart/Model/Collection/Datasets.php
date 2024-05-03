<?php
/**
 * @package  trade Datasets.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model\Collection;

use App\Core\Components\Chart\Model\DataSet;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class Datasets extends ArrayCollection
{
    /**
     * @param DataSet[] $elements
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!($element instanceof DataSet)) {
                throw new InvalidArgumentException("Element must be a DataSet");
            }
        }
        parent::__construct($elements);
    }

    /**
     * @param DataSet $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof DataSet)) {
            throw new InvalidArgumentException("Element must be an instance of DataSet");
        }

        parent::add($element);
    }

    /**
     * @return DataSet[]
     */
    public function toArray(): array
    {
        return array_map(static fn(DataSet $dataset) => $dataset->toArray(), parent::toArray());
    }

    public static function fromArray(iterable $array): Datasets
    {
        if ($array instanceof self) {
            return $array;
        }
        return new Datasets(array_map(static fn($e) => DataSet::fromArray($e), (array)$array));
    }
}