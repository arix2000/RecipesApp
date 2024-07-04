<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand("app:change-user-password", "Change the password of an existing user")]
class ChangeUserPasswordCommand extends Command
{
    protected static $defaultName = 'app:change-user-password';
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
        $this->setHelp('This command allows you to change the password of an existing user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $users = $this->entityManager->getRepository(User::class)->findBy([], ['id' => 'ASC']);
        if (empty($users)) {
            $io->error('No users found.');
            return Command::FAILURE;
        }

        $userChoices = [];
        foreach ($users as $user) {
            $userChoices[$user->getId()] = sprintf(': %s %s  |  (%s)', $user->getFirstName(), $user->getLastName(), $user->getEmail());
        }

        $question = new ChoiceQuestion('Select the user to change the password:', $userChoices);
        $question->setErrorMessage('User %s is invalid.');
        $selectedUserId = array_search($helper->ask($input, $output, $question), $userChoices);

        $selectedUser = $this->entityManager->getRepository(User::class)->find($selectedUserId);
        if (!$selectedUser) {
            $io->error('User not found.');
            return Command::FAILURE;
        }

        $passwordQuestion = new Question('Enter the new password:');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $passwordQuestion->setAutocompleterValues(null); // Disable autocomplete
        $newPassword = $helper->ask($input, $output, $passwordQuestion);

        $hashedPassword = $this->passwordHasher->hashPassword($selectedUser, $newPassword);
        $selectedUser->setPassword($hashedPassword);

        $this->entityManager->flush();

        $io->success(sprintf('Password for user %s %s has been changed.', $selectedUser->getFirstName(), $selectedUser->getLastName()));

        return Command::SUCCESS;
    }
}
