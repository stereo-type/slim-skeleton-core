<?php
/**
 * @package  trade Labels.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class Labels extends ArrayCollection
{
    /**
     * @param String[] $elements
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!is_string($element)) {
                throw new InvalidArgumentException("Element must be a string");
            }
        }
        parent::__construct($elements);
    }

    /**
     * @param String $element
     * @return void
     */
    public function add($element): void
    {
        if (!is_string($element)) {
            throw new InvalidArgumentException("Element must be an instance of string");
        }

        parent::add($element);
    }

    /**
     * @return String[]
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    public static function fromArray(iterable $array): Labels
    {
        if ($array instanceof self) {
            return $array;
        }
        return new Labels(array_map(static fn ($e) => (string)$e, (array)$array));
    }
}
