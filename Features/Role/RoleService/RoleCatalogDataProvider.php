<?php
/**
 * @package  TableDataProvider.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Features\Role\RoleService;

use App\Core\Components\Catalog\Providers\EntityDataProvider;
use App\Core\Features\Role\Entity\Role;
use App\Core\Services\HashService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class RoleCatalogDataProvider extends EntityDataProvider
{
    public const ENTITY_CLASS = Role::class;

    public function exclude_entity_properties(): array
    {
        return ['permissions', 'assignments'];
    }

    public function exclude_entity_filters(): array
    {
        return ['updatedAt', 'createdAt', 'permissions', 'assignments'];
    }

    public function exclude_form_elements(array $args): array
    {
        return ['updatedAt', 'createdAt'];
    }

    public function named_properties(): array
    {
        return [
            'name'        => 'Имя',
            'fullname'    => 'Название',
            'description' => 'Описание',
            'archetype'   => 'Архетип',
            'createdAt'   => 'Создан',
            'updatedAt'   => 'Изменен',
            'id'          => 'ID',
        ];
    }

    /**
     * @param object $instance
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function before_save(object $instance): void
    {
//        $instance->setPassword($this->container->get(HashService::class)->hashPassword($instance->getPassword()));
    }

    public function before_set(object $instance): void
    {
//        $instance->setPassword('');
    }


}
