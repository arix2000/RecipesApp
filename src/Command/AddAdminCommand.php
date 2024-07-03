<?php

namespace App\Command;

use App\Entity\User;
use App\Model\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddAdminCommand extends Command
{
    protected static $defaultName = 'app:add-admin';
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->setName("app:add-admin")
            ->setDescription('Add a new admin user')
            ->setHelp('This command allows you to create a new admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $output->writeln([
            'Admin User Creator',
            '==================',
            '',
        ]);

        $questions = [
            'firstName' => new Question('First Name: '),
            'lastName' => new Question('Last Name: '),
            'email' => new Question('Email: '),
            'password' => new Question('Password (input will not be visible): '),
        ];

        $questions['password']->setHidden(true);
        $questions['password']->setHiddenFallback(false);

        $userData = [];
        foreach ($questions as $key => $question) {
            $userData[$key] = $helper->ask($input, $output, $question);
        }

        $user = User::from(
            $userData['firstName'],
            $userData['lastName'],
            $userData['email'],
            $this->passwordHasher->hashPassword(new User(), $userData['password']),
            UserRole::ROLE_ADMIN
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Admin user successfully generated!');

        return Command::SUCCESS;
    }
}
