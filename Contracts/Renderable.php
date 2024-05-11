<?php
/**
 * @package  trade Rendarable.php
 * @copyright 11.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Contracts;

use Slim\Views\Twig;

interface Renderable
{
    public function render(Twig $twig, string $template): string;
}
