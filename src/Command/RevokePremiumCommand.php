<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'RevokePremiumCommand',
    description: 'Revokes the premium status of a user',
)]
class RevokePremiumCommand extends Command
{

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManagerInterface
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'The id of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userId = $input->getArgument('id');
        $user = $this->userRepository->findOneById($userId);
        if ($user == null) {
            $io->error('User not found');
            return Command::FAILURE;
        }
        $user->setPremium(false);
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();
        $io->success('User is not premium anymore');
        return Command::SUCCESS;
    }
}
