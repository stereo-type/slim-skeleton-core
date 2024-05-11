<?php
/**
 * @package  trade LinkableSettingsPage.php
 * @copyright 06.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree\Interfaces;

use Psr\Http\Message\UriInterface;

interface LinkableSettingsPage
{
    /**
     * Get the URL to view this settings page.
     * @return UriInterface
     */
    public function get_settings_page_url(): UriInterface;
}
