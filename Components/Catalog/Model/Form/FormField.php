<?php
/**
 * @package  trade FormField.php
 * @copyright 27.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Form;

readonly class FormField
{

    /**
     * @param string $fieldName
     * @param string $fieldType - класс - наследник Symfony\Component\Form\AbstractType
     * @param array $options
     */
    public function __construct(public string $fieldName, public string $fieldType, public array $options = [])
    {
    }
}