<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Road;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RoadFixtures extends Fixture implements DependentFixtureInterface
{
    public const ROAD_PARIS_LYON = 'ROAD_PARIS_LYON';
    public const ROAD_LYON_MARSEILLE = 'ROAD_LYON_MARSEILLE';

    public function load(ObjectManager $manager): void
    {
        $road1 = new Road();
        $road1->setStartCity($this->getReference('CITY_PARIS', City::class));
        $road1->setArrivedCity($this->getReference('CITY_LYON', City::class));
        $road1->setEstimatedTime(new \DateTime('02:00:00'));
        $manager->persist($road1);
        $this->addReference(self::ROAD_PARIS_LYON, $road1);

        $road2 = new Road();
        $road2->setStartCity($this->getReference('CITY_LYON', City::class));
        $road2->setArrivedCity($this->getReference('CITY_MARSEILLE', City::class));
        $road2->setEstimatedTime(new \DateTime('03:30:00'));
        $manager->persist($road2);
        $this->addReference(self::ROAD_LYON_MARSEILLE, $road2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
        ];
    }
}