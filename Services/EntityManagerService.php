<?php

declare(strict_types=1);

namespace App\Core\Services;

use BadMethodCallException;
use App\Core\Contracts\EntityManagerServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @mixin EntityManagerInterface
 */
readonly class EntityManagerService implements EntityManagerServiceInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->entityManager, $name)) {
            return call_user_func_array([$this->entityManager, $name], $arguments);
        }

        throw new BadMethodCallException('Call to undefined method "' . $name . '"');
    }

    public function sync(?object $entity = null): void
    {
        if ($entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    public function delete(object $entity, bool $sync = false): void
    {
        $this->entityManager->remove($entity);

        if ($sync) {
            $this->sync();
        }
    }

    public function clear(?string $entityName = null): void
    {
        if ($entityName === null) {
            $this->entityManager->clear();

            return;
        }

        $unitOfWork = $this->entityManager->getUnitOfWork();
        $entities = $unitOfWork->getIdentityMap()[$entityName] ?? [];

        foreach ($entities as $entity) {
            $this->entityManager->detach($entity);
        }
    }

    public function enableUserAuthFilter(int $userId): void
    {
        $this->getFilters()->enable('user')->setParameter('user_id', $userId);
    }

    /**
     * @param string $className
     * @param $id
     * @return mixed
     * @throws EntityNotFoundException
     */
    public function get(string $className, $id): mixed
    {
        $instance = $this->entityManager->find($className, $id);
        if ($instance) {
            return $instance;
        }
        throw new EntityNotFoundException('Class "' . $className . '" instance with id "' . $id . '" not found');
    }
}
