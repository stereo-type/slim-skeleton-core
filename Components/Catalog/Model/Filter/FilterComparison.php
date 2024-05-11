<?php

namespace App\Core\Components\Catalog\Model\Filter;

readonly class FilterComparison
{
    /**
     * @param string $name
     * @param string $condition использовать константы из Doctrine\Common\Collections\Expr\Comparison
     * @param bool $manyToOne
     * @param string|null $manyToOneClass
     */
    public function __construct(
        public string $name,
        public string $condition,
        public bool $manyToOne = false,
        public ?string $manyToOneClass = null,
    ) {


    }

}
