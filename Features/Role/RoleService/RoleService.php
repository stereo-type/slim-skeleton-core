<?php

namespace App\Core\Features\Role\RoleService;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Features\Role\Entity\Role;
use App\Core\Features\User\Contracts\AuthInterface;
use App\Core\Features\User\Contracts\UserInterface;
use Doctrine\DBAL\Connection;

readonly class RoleService
{
    public function __construct(
        private EntityManagerServiceInterface $entityManager,
        private AuthInterface $authService
    ) {
    }

    /**
     * @return void
     */
    public function create_default_roles(): void
    {
        $role = new Role();
        $role->setName(Role::ADMIN);
        $role->setFullname(Role::ADMIN);
        $this->entityManager->sync($role);
        $role = new Role();
        $role->setName(Role::MANAGER);
        $role->setFullname(Role::MANAGER);
        $this->entityManager->sync($role);
        $role = new Role();
        $role->setName(Role::CUSTOMER);
        $role->setFullname(Role::CUSTOMER);
        $this->entityManager->sync($role);
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function remove_default_roles(Connection $connection): void
    {
        $user = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ADMIN]);
        if ($user) {
            $this->entityManager->delete($user);
        }
        $user = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::MANAGER]);
        if ($user) {
            $this->entityManager->delete($user);
        }
        $user = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::CUSTOMER]);
        if ($user) {
            $this->entityManager->delete($user);
        }
        $this->entityManager->sync();
    }


    public function createRole($name): Role
    {
        $role = new Role();
        $role->setName($name);
        $this->entityManager->sync($role);

        return $role;
    }


    public function isAdmin(UserInterface $user): bool
    {
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                if ($role->getName() == Role::ADMIN) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isAdminCurrentUser(): bool
    {
        return $this->isAdmin($this->authService->loggedUser());
    }


}
