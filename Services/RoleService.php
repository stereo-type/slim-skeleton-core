<?php
/**
 * @package  build_rent RoleService.php
 * @copyright 11.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Entity\Role;
use App\Core\Entity\RoleAssignment;
use App\Core\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class RoleService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function addUserRole(User $user, Role $role, bool $flush = true): void
    {
        $assignment = new RoleAssignment();
        $assignment->setRole($role);
        $assignment->setUser($user);
        $this->entityManager->persist($assignment);
        $this->entityManager->flush();
    }

    public function removeUserRole(User $user, Role $role, bool $flush = true): void
    {
        $assignment = $this->entityManager->getRepository(self::class)->findOneBy(['user' => $user, 'role' => $role]);
        if ($assignment) {
            $this->entityManager->remove($assignment);
            $this->entityManager->flush();
        }
    }

    public function changeUserRole(User $user, Role $oldRole, Role $newRole, bool $flush = true): void
    {
        $this->removeUserRole($user, $oldRole, false);
        $this->addUserRole($user, $newRole, $flush);
    }
}