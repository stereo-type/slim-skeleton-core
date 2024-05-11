<?php

declare(strict_types=1);

namespace App\Core\Filters;

use App\Core\Container;
use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Contracts\User\UserInterface;
use App\Core\Entity\User;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use App\Core\Contracts\User\OwnableInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserFilter extends SQLFilter
{
    public static array $_users = [];

    /**
     * @param string $parameter
     * @return UserInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getUser(string $parameter): UserInterface
    {
        $id = str_replace("'", '', $parameter);
        if(array_key_exists($id, self::$_users)) {
            return self::$_users[$id];
        }
        $container = Container::get_container();
        $user = $container
            ->get(EntityManagerServiceInterface::class)
            ->getRepository(User::class)
            ->find($id);
        self::$_users[$id] = $user;
        return $user;
    }

    /**
     * @param ClassMetadata $targetEntity
     * @param $targetTableAlias
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->getReflectionClass() !== null
            && !$targetEntity->getReflectionClass()->implementsInterface(OwnableInterface::class)) {
            return '';
        }


        $user = $this->getUser($this->getParameter('user_id'));
        if($user->isAdmin()) {
            return '';
        }

        return $targetTableAlias . '.user_id = ' . $this->getParameter('user_id');
    }
}
