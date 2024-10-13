<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setFirstname("Admin");
        $user->setLastname("Admin");
        $user->setEmail("admin@admin.com");
        $user->setPassword($this->passwordHasher->hashPassword($user, "admin"));

        $manager->persist($user);

        $user = new User();
        $user->setFirstname("User");
        $user->setLastname("User");
        $user->setEmail("user@user.com");
        $user->setPassword($this->passwordHasher->hashPassword($user, "user"));

        $manager->persist($user);


        $manager->flush();
    }
}
