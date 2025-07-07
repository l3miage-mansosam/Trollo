<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_ADMIN = 'USER_ADMIN';
    public const USER_CLIENT = 'USER_CLIENT';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin.root@gmail.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('System');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminroot'));
        $admin->setRole($this->getReference('ROLE_ADMIN', Role::class));
        $manager->persist($admin);
        $this->addReference(self::USER_ADMIN, $admin);

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $user->setRole($this->getReference('ROLE_USER', Role::class));
        $manager->persist($user);
        $this->addReference(self::USER_CLIENT, $user);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixtures::class,
        ];
    }
}