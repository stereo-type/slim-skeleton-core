<?php

declare(strict_types=1);

namespace App\Core\Features\User\Services;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Features\Auth\DTO\RegisterUserData;
use App\Core\Features\User\Contracts\UserInterface;
use App\Core\Features\User\Contracts\UserProviderServiceInterface;
use App\Core\Features\User\Entity\User;
use App\Core\Services\HashService;
use DateTime;

readonly class UserProviderService implements UserProviderServiceInterface
{
    public function __construct(
        private EntityManagerServiceInterface $entityManager,
        private HashService $hashService
    ) {
    }

    public function getById(int $userId): ?UserInterface
    {
        return $this->entityManager->find(User::class, $userId);
    }

    public function getByCredentials(array $credentials): ?UserInterface
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function createUser(RegisterUserData $data): UserInterface
    {
        $user = new User();

        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword($this->hashService->hashPassword($data->password));

        $this->entityManager->sync($user);

        return $user;
    }

    public function verifyUser(UserInterface $user): void
    {
        $user->setVerifiedAt(new DateTime());

        $this->entityManager->sync($user);
    }

    public function updatePassword(UserInterface $user, string $password): void
    {
        $user->setPassword($this->hashService->hashPassword($password));

        $this->entityManager->sync($user);
    }
}
