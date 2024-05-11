<?php

declare(strict_types=1);

namespace App\Core\Features\User\Contracts;

/**Интерфейс подключаемый к сущностям имеющим связь с пользователем,
 * если подключен интерфейс то в запросы данной сущности будет использоваться SQLFilter
 */
interface OwnableInterface
{
    public function getUser(): UserInterface;
}
