<?php

namespace App\DataFixtures;

use App\Entity\Bus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BusFixtures extends Fixture
{
    public const BUS_1 = 'BUS_1';
    public const BUS_2 = 'BUS_2';

    public function load(ObjectManager $manager): void
    {
        $bus1 = new Bus();
        $bus1->setName('Bus Express 1');
        $bus1->setImmatriculation('AA-123-BB');
        $bus1->setModel('Mercedes Tourismo');
        $bus1->setCapacity(50);
        $manager->persist($bus1);
        $this->addReference(self::BUS_1, $bus1);

        $bus2 = new Bus();
        $bus2->setName('Bus Express 2');
        $bus2->setImmatriculation('CC-456-DD');
        $bus2->setModel('Volvo 9700');
        $bus2->setCapacity(45);
        $manager->persist($bus2);
        $this->addReference(self::BUS_2, $bus2);

        $manager->flush();
    }
}