<?php

namespace App\Core\Repository\Role;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Contracts\User\UserInterface;
use App\Core\Entity\Role;
use Doctrine\DBAL\Connection;


readonly class RoleService
{

    public function __construct(private EntityManagerServiceInterface $entityManager)
    {
    }

    /**
     * @return void
     */
    public function create_default_roles(): void
    {
        $role = new Role();
        $role->setName(Role::ADMIN);
        $this->entityManager->sync($role);
        $role = new Role();
        $role->setName(Role::MANAGER);
        $this->entityManager->sync($role);
        $role = new Role();
        $role->setName(Role::CUSTOMER);
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


}
