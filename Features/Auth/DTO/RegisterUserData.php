<?php

declare(strict_types=1);

namespace App\Core\Features\Auth\DTO;

readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }
}
