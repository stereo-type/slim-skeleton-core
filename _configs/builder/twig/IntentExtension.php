<?php
/**
 * @package  StimulusExtension.php
 * @copyright 24.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\_configs\builder\twig;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;

class IntentExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('indent', [$this, 'indent']),
        ];
    }

    public function indent($text, $count)
    {
        if ($text instanceof Markup) {
            $text = (string)$text;
        }

        return preg_replace('/^/m', str_repeat('&nbsp;', $count), $text);
    }

}
