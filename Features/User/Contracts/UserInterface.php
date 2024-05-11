<?php

declare(strict_types=1);

namespace App\Core\Features\User\Contracts;

use DateTime;

interface UserInterface
{
    public function getId(): int;

    public function getPassword(): string;

    public function setVerifiedAt(DateTime $verifiedAt): static;

    public function isTwoFactor(): bool;

    public function getRoles(): array;

    public function isAdmin(): bool;
}
