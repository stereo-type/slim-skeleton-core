<?php
/**
 * @package  trade Options.php
 * @copyright 29.04.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Chart\Model\Collection;

use App\Core\Components\Chart\Model\Option;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class Options extends ArrayCollection
{
    /**
     * @param  Option[]  $elements
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!($element instanceof Option)) {
                throw new InvalidArgumentException("Element must be an instance of Option");
            }
        }
        parent::__construct($elements);
    }

    /**
     * @param Option $element
     * @return void
     */
    public function add($element): void
    {
        if (!($element instanceof Option)) {
            throw new InvalidArgumentException("Element must be an instance of Option");
        }

        parent::add($element);
    }

    /**Переопределен ради phpdoc
     * @return Option[]
     */
    public function toArray(): array
    {
        $options = array_map(static fn(Option $option) => $option->toArray(), parent::toArray());
        return array_merge(...$options);
    }

    public function toMap(): array
    {
        $result = [];
        foreach ($this->toArray() as $option) {
            $result[$option->key] = $option->value;
        }
        return $result;
    }

    public function __toString(): string
    {
        return implode(' ', $this->toArray());
    }

    public static function fromArray(iterable $array): Options
    {
        if ($array instanceof self) {
            return $array;
        }

        $attributes = [];
        foreach ($array as $key => $value) {
            if ($value instanceof Option) {
                $attributes [] = $value;
            } else {
                $attributes [] = new Option($key, $value);
            }
        }
        return new Options($attributes);
    }


    public function remove(string|int $key): ?Option
    {
        $removed = null;
        foreach ($this->toArray() as $index => $element) {
            if ($element->key === $key) {
                $removed = $element;
                parent::remove($index);
                break;
            }
        }
        return $removed;
    }


}