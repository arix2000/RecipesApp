<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user0 = new User("root", "root", "root@root.com", "Root1234");
        $user1 = new User("Arkadiusz", "Mądry", "arek@gmail.com", "Test1234");
        $user2 = new User("Jan", "Rembikowski", "jan@gmail.com", "Test1234");
        $user3 = new User("Maciek", "Kiełducki", "maciek@gmail.com", "Test1234");
        $manager->persist($user0);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user3);
        printf("\033[0;32mAll user added \033[0m\n");
        $manager->flush();
        $this->addReference('user_reference', $user0);
    }
}