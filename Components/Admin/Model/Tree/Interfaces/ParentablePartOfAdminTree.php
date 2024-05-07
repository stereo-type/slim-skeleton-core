<?php
/**
 * @package  trade ParentablePartOfAdminTree.php
 * @copyright 06.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree\Interfaces;

interface ParentablePartOfAdminTree extends PartOfAdminTree {

    /**
     * @param string $destinationname The internal name of the new parent for $something.
     * @param PartOfAdminTree $something The object to be added.
     * @return bool True on success, false on failure.
     */
    public function add(string $destinationname, PartOfAdminTree $something, ?string $beforesibling = null): bool;

}