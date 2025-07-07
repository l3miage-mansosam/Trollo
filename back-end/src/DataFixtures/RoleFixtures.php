<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public function load(ObjectManager $manager): void
    {
        $roleUser = new Role();
        $roleUser->setName('USER');
        $manager->persist($roleUser);
        $this->addReference(self::ROLE_USER, $roleUser);

        $roleAdmin = new Role();
        $roleAdmin->setName('ADMIN');
        $manager->persist($roleAdmin);
        $this->addReference(self::ROLE_ADMIN, $roleAdmin);

        $manager->flush();
    }
}