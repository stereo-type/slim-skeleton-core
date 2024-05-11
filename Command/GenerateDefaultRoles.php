<?php

declare(strict_types=1);

namespace App\Core\Command;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Features\User\Contracts\AuthInterface;
use App\Core\Repository\Role\RoleService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class GenerateDefaultRoles extends Command
{
    protected static $defaultName = 'app:generate-roles';
    protected static $defaultDescription = 'Generates default roles';

    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly AuthInterface $authService
    ) {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $service = new RoleService($this->entityManager, $this->authService);
            $service->create_default_roles();
        } catch (Throwable $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
