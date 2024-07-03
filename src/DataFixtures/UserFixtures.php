<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Model\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user0 = User::from("root", "root", "root@root.com", "root", UserRole::ROLE_ADMIN);
        $user0->setPassword($this->passwordHasher->hashPassword($user0, $user0->getPassword()));
        $user1 = User::from("Arkadiusz", "Mądry", "arek@gmail.com", "test", UserRole::ROLE_USER);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, $user1->getPassword()));
        $user2 = User::from("Jan", "Rembikowski", "jan@gmail.com", "test", UserRole::ROLE_USER);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, $user2->getPassword()));
        $user3 = User::from("Maciek", "Kiełducki", "maciek@gmail.com", "test", UserRole::ROLE_USER);
        $user3->setPassword($this->passwordHasher->hashPassword($user3, $user3->getPassword()));
        $manager->persist($user0);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user3);
        printf("\033[0;32mAll user added \033[0m\n");
        $manager->flush();
        $this->addReference('user_reference', $user0);
    }
}