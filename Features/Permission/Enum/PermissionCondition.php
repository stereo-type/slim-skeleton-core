<?php

namespace App\Core\Features\Permission\Enum;

enum PermissionCondition: string
{
    case all = 'all';
    case any = 'any';
}
