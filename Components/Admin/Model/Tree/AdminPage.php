<?php
/**
 * @package  trade AdminPage.php
 * @copyright 06.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree;

use App\Core\Components\Admin\Model\Tree\Interfaces\PartOfAdminTree;
use App\Core\Components\Admin\Model\Tree\Interfaces\LinkableSettingsPage;
use Psr\Http\Message\UriInterface;
use stdClass;

class AdminPage implements PartOfAdminTree, LinkableSettingsPage
{
    public array $visiblepath;
    public array $path;
    public bool $is_category;

    /**
     * Constructor for adding an external page into the admin tree.
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visibleName
     * @param UriInterface $url
     * @param boolean $hidden Is this external page hidden in admin tree block? Default false.
     */
    public function __construct(
        readonly public string $name,
        readonly public string $visibleName,
        readonly public UriInterface $url,
        readonly public bool $hidden = false,
    ) {
        $this->is_category = false;
    }

    public function get_settings_page_url(): UriInterface
    {
        return $this->url;
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @return PartOfAdminTree|null A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    public function locate(string $name, bool $findpath = false): ?PartOfAdminTree
    {
        if ($this->name === $name) {
            if ($findpath) {
                $this->visiblepath = [$this->visibleName];
                $this->path = [$this->name];
            }
            return $this;
        }

        return null;
    }

    /**
     * This function always returns false, required function by interface
     * @param string $name
     * @return false
     */
    public function prune(string $name): bool
    {
        return false;
    }

    /**
     * Search using query
     *
     * @param string $query
     * @return mixed array-object structure of found settings and pages
     */
    public function search(string $query): array
    {
        $found = false;
        if (str_contains(strtolower($this->name), $query)) {
            $found = true;
        } elseif (str_contains(mb_strtolower($this->visibleName), $query)) {
            $found = true;
        }
        if ($found) {
            $result = new stdClass();
            $result->page = $this;
            $result->settings = array();
            return [$this->name => $result];
        }

        return [];
    }

    public function check_access(): bool
    {
        return true;
    }


    public function is_hidden(): bool
    {
        return $this->hidden;
    }

}
