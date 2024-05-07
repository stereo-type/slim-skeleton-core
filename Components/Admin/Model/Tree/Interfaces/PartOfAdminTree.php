<?php
/**
 * @package  trade PartOfAdminTree.php
 * @copyright 06.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree\Interfaces;

interface PartOfAdminTree
{
    /**
     * @param string $name
     * @return PartOfAdminTree|null An object reference or a NULL reference.
     */
    public function locate(string $name): ?PartOfAdminTree;

    /**
     *
     * @param string $name The internal name we want to remove.
     * @return bool success.
     */
    public function prune(string $name): bool;

    /**
     * Search using query
     * @param string $query
     * @return array array-object structure of found settings and pages
     */
    public function search(string $query): array;

    /**
     * Verifies current user's access to this part.
     * @return bool True if the user has access, false if she doesn't.
     */
    public function check_access(): bool;

    /**
     * @return bool
     */
    public function is_hidden(): bool;

}