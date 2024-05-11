<?php
/**
 * @package  Category.php
 * @copyright 24.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree;

use App\Core\Components\Admin\Model\Tree\Interfaces\LinkableSettingsPage;
use App\Core\Components\Admin\Model\Tree\Interfaces\ParentablePartOfAdminTree;
use App\Core\Components\Admin\Model\Tree\Interfaces\PartOfAdminTree;
use App\Core\Lib\Url;
use Psr\Http\Message\UriInterface;

class AdminCategory implements ParentablePartOfAdminTree, LinkableSettingsPage
{
    protected array $category_cache;
    public array $visiblepath;
    public array $path;
    public bool $is_category;

    /**
     * @param string $name
     * @param string $visibleName
     * @param PartOfAdminTree[] $children
     * @param bool $hidden
     */
    public function __construct(
        readonly public string $name,
        readonly public string $visibleName,
        public array $children = [],
        readonly public bool $hidden = false,
    ) {
        $this->is_category = true;
    }

    public function get_settings_page_url(): UriInterface
    {
        return Url::build('/admin/category', ['category' => $this->name]);
    }

    public function add(string $destinationname, PartOfAdminTree $something, ?string $beforesibling = null): bool
    {
        $parent = $this->locate($destinationname);

        if (is_null($parent)) {
            debugging('parent does not exist!');
            return false;
        }
        if (!($parent instanceof ParentablePartOfAdminTree)) {
            debugging('error - parts of tree can be inserted only into parentable parts');
            return false;
        }

        if (!is_null($this->locate($something->name))) {
            debugging('Duplicate admin page name: ' . $something->name, DEBUG_DEVELOPER);
        }
        if (is_null($beforesibling)) {
            // Append $something as the parent's last child.
            $parent->children [] = $something;
        } else {
            // Try to find the position of the sibling.
            $siblingposition = null;
            foreach ($parent->children as $childposition => $child) {
                if ($child->name === $beforesibling) {
                    $siblingposition = $childposition;
                    break;
                }
            }
            if (is_null($siblingposition)) {
                debugging('Sibling ' . $beforesibling . ' not found', DEBUG_DEVELOPER);
                $parent->children[] = $something;
            } else {
                $parent->children = array_merge(
                    array_slice($parent->children, 0, $siblingposition),
                    [$something],
                    array_slice($parent->children, $siblingposition)
                );
            }
        }
        if ($something instanceof self) {
            if (isset($this->category_cache[$something->name])) {
                debugging('Duplicate admin category name: ' . $something->name);
            } else {
                $this->category_cache[$something->name] = $something;
                $something->category_cache = & $this->category_cache;
                foreach ($something->children as $child) {
                    // just in case somebody already added subcategories
                    if ($child instanceof self) {
                        if (isset($this->category_cache[$child->name])) {
                            debugging('Duplicate admin category name: ' . $child->name);
                        } else {
                            $this->category_cache[$child->name] = $child;
                            $child->category_cache = & $this->category_cache;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function locate(string $name, bool $findpath = false): ?PartOfAdminTree
    {
        if (!isset($this->category_cache[$this->name])) {
            $this->category_cache[$this->name] = $this;
        }

        if ($this->name === $name) {
            if ($findpath) {
                $this->visiblepath[] = $this->visibleName;
                $this->path[] = $this->name;
            }
            return $this;
        }

        if (!$findpath && isset($this->category_cache[$name])) {
            return $this->category_cache[$name];
        }

        $return = null;
        foreach ($this->children as $childid => $unused) {
            if ($return = $this->children[$childid]->locate($name, $findpath)) {
                break;
            }
        }

        if (!is_null($return) && $findpath) {
            $return->visiblepath[] = $this->visibleName;
            $return->path[] = $this->name;
        }

        return $return;
    }

    public function prune(string $name): bool
    {
        if ($this->name === $name) {
            return false;
        }

        foreach ($this->children as $precedence => $child) {
            if ($child->name === $name) {
                while ($this->category_cache) {
                    array_pop($this->category_cache);
                }
                unset($this->children[$precedence]);
                return true;
            }

            if ($this->children[$precedence]->prune($name)) {
                return true;
            }
        }
        return false;
    }

    public function search(string $query): array
    {
        $result = [];
        foreach ($this->children as $child) {
            $subsearch = $child->search($query);
            if (!is_array($subsearch)) {
                debugging('Incorrect search result from ' . $child->name);
                continue;
            }
            $result = array_merge($result, $subsearch);
        }
        return $result;
    }

    public function check_access(): bool
    {
        foreach ($this->children as $child) {
            if ($child->check_access()) {
                return true;
            }
        }
        return false;
    }

    public function is_hidden(): bool
    {
        return $this->hidden;
    }
}
