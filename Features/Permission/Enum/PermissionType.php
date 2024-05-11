<?php

namespace App\Core\Features\Permission\Enum;

enum PermissionType: string
{
    case read = 'read';
    case write = 'write';
}
