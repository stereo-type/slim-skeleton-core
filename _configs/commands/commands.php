<?php

declare(strict_types = 1);

/**Список команд для консольного приложения*/
use App\Core\Command\GenerateAppKeyCommand;
use App\Core\Command\GenerateDefaultRoles;
use App\Core\Command\MakeUserAdmin;

return [
    GenerateAppKeyCommand::class,
    GenerateDefaultRoles::class,
    MakeUserAdmin::class,
];
