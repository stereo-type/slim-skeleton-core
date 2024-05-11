<?php

namespace App\Core\Features\Role\Role;

use App\Core\Features\Role\Entity\Role;

interface RoleAssignmentInterface
{

    public function getRole(): Role;
}
