<?php

namespace App\Core\Contracts\Role;

use App\Core\Entity\Role;

interface RoleAssignmentInterface
{

    public function getRole(): Role;
}