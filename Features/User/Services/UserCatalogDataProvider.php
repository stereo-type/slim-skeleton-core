<?php
/**
 * @package  TableDataProvider.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Features\User\Services;

use App\Core\Components\Catalog\Providers\EntityDataProvider;
use App\Core\Features\User\Entity\User;
use App\Core\Services\HashService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserCatalogDataProvider extends EntityDataProvider
{
    public const ENTITY_CLASS = User::class;

    public function exclude_entity_properties(): array
    {
        return ['password', 'assignments'];
    }

    public function exclude_entity_filters(): array
    {
        return ['updatedAt', 'createdAt', 'verifiedAt', 'assignments', 'twoFactor'];
    }

    public function exclude_form_elements(array $args): array
    {
        return ['twoFactor', 'updatedAt', 'createdAt', 'verifiedAt'];
    }

    public function named_properties(): array
    {
        return [
            'twoFactor'  => '2FA',
            'name'       => "Имя",
            'verifiedAt' => 'Подтвержден',
            'createdAt'  => 'Создан',
            'updatedAt'  => 'Изменен',
            'id'         => 'ID',
            'email'      => 'E-mail'
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
        $instance->setPassword($this->container->get(HashService::class)->hashPassword($instance->getPassword()));
    }

    public function before_set(object $instance): void
    {
        $instance->setPassword('');
    }


}
