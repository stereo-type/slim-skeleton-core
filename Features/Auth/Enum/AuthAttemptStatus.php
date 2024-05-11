<?php

declare(strict_types=1);

namespace App\Core\Features\Auth\Enum;

enum AuthAttemptStatus
{
    case FAILED;
    case TWO_FACTOR_AUTH;
    case SUCCESS;
}
