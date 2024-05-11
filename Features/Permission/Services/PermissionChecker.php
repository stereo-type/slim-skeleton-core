<?php

namespace App\Core\Features\Permission\Services;

use App\Core\Exception\PermissionException;
use App\Core\Features\Permission\Enum\PermissionCondition;
use App\Core\Features\Role\Entity\Role;
use App\Core\Features\User\Contracts\UserInterface;
use Exception;
use Throwable;

readonly class PermissionChecker
{
    /**
     * @param UserInterface $user
     * @param string[] $requiredPermissions
     * @param PermissionCondition $condition
     * @return bool
     * @throws Exception
     */
    public function hasPermission(
        UserInterface $user,
        array $requiredPermissions,
        PermissionCondition $condition = PermissionCondition::all
    ): bool {
        if (empty($requiredPermissions)) {
            return true;
        }
        $hasPermissions = array_combine($requiredPermissions, array_fill(0, count($requiredPermissions), false));
        $roles = $user->getRoles();
        if ($roles->isEmpty()) {
            return false;
        }
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                /***admin has all permissions**/
                if ($role->getName() == Role::ADMIN) {
                    return true;
                }
                foreach ($role->getPermissions() as $permission) {
                    $name = $permission->getName();
                    if (in_array($name, $requiredPermissions) && !$hasPermissions[$name]) {
                        $hasPermissions[$name] = true;
                    }
                }
            }
        }

        switch ($condition) {
            case PermissionCondition::all:
                $has = array_filter($hasPermissions, static fn ($val) => $val);
                return count($has) === count($requiredPermissions);
            case PermissionCondition::any:
                return !empty(array_filter($hasPermissions, static fn ($val) => $val));
        }

        return false;
    }

    /**
     * @param UserInterface $user
     * @param array $requiredPermissions
     * @param PermissionCondition $condition
     * @return void
     */
    public function requirePermission(
        UserInterface $user,
        array $requiredPermissions,
        PermissionCondition $condition = PermissionCondition::all
    ): void {
        try {
            $has = $this->hasPermission($user, $requiredPermissions, $condition);
            if (!$has) {
                throw new PermissionException($requiredPermissions);
            }
        } catch (Throwable $e) {
            throw new PermissionException($requiredPermissions, message: $e->getMessage());
        }
    }
}
