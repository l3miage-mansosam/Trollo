<?php

namespace App\DataFixtures;

use App\Entity\Bus;
use App\Entity\City;
use App\Entity\Road;
use App\Entity\Session;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SessionFixtures extends Fixture implements DependentFixtureInterface
{
    public const SESSION_1 = 'SESSION_1';
    public const SESSION_2 = 'SESSION_2';

    public function load(ObjectManager $manager): void
    {
        $session1 = new Session();
        $session1->setRoad($this->getReference('ROAD_PARIS_LYON', Road::class));
        $session1->setBus($this->getReference('BUS_1', Bus::class));
        $session1->setUnitPrice(35.00);
        $session1->setDepartureDate(new \DateTime('+1 week 10:00'));
        $session1->setEstimatedTime(new \DateTime('02:00:00'));
        $session1->setStartCity($this->getReference('CITY_PARIS', City::class));
        $session1->setArrivedCity($this->getReference('CITY_LYON', City::class));
        $manager->persist($session1);
        $this->addReference(self::SESSION_1, $session1);

        $session2 = new Session();
        $session2->setRoad($this->getReference('ROAD_LYON_MARSEILLE', Road::class));
        $session2->setBus($this->getReference('BUS_2', Bus::class));;
        $session2->setUnitPrice(45.00);
        $session2->setDepartureDate(new \DateTime('+1 week 14:00'));
        $session2->setEstimatedTime(new \DateTime('03:30:00'));
        $session2->setStartCity($this->getReference('CITY_MARSEILLE', City::class));
        $session2->setArrivedCity($this->getReference('CITY_LYON', City::class));
        $manager->persist($session2);
        $this->addReference(self::SESSION_2, $session2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoadFixtures::class,
            BusFixtures::class,
        ];
    }
}