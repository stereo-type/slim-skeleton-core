<?php

declare(strict_types=1);

namespace App\Core\Command;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Entity\Role;
use App\Core\Entity\User;
use App\Core\Services\RoleService;
use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeUserAdmin extends Command
{
    protected static $defaultName = 'app:make-admin';
    protected static $defaultDescription = 'Make user admin';
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly RoleService $roleService,
    ) {
        parent::__construct();
    }


    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
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
            $io = $this->io;
            $io->title(self::$defaultDescription);
            $id = (int)$io->ask('Enter id of user?');
            if (!$id) {
                $this->io->error('Invalid id');
                return Command::INVALID;
            }
            $user = $this->entityManager->find(User::class, $id);
            $role = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => Role::ADMIN]);

            if (!$user) {
                $this->io->error('No user found with id' . $id);
                return Command::INVALID;
            }

            if (!$role) {
                $this->io->error('Role ADMIN not exist');
                return Command::FAILURE;
            }

            if ($user->isAdmin()) {
                $this->io->error('User is already admin');
                return Command::INVALID;
            }

            if (!$input->isInteractive() ||
                $this->io->confirm('Userid = ' . $id . ' will be admin. Are you sure?', false)) {
                $this->roleService->addUserRole($user, $role);
            } else {
                $this->io->error('Canceled');
                return Command::FAILURE;
            }
            $this->io->success('Success');
        } catch (Throwable $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

