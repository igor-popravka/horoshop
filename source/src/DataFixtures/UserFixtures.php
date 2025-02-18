<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setLogin('admin')
            ->setPass('admin')
            ->setPhone('12345678')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        $user = new User();
        $user->setLogin('user')
            ->setPass('user')
            ->setPhone('12345679')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->hasher->hashPassword($user, 'user'));
        $manager->persist($user);

        $manager->flush();
    }
}
