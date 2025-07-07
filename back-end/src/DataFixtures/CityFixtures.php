<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public const CITY_PARIS = 'CITY_PARIS';
    public const CITY_LYON = 'CITY_LYON';
    public const CITY_MARSEILLE = 'CITY_MARSEILLE';

    public function load(ObjectManager $manager): void
    {
        $cities = [
            ['Paris', 'France'],
            ['Lyon', 'France'],
            ['Marseille', 'France'],
        ];

        foreach ($cities as $i => [$name, $country]) {
            $city = new City();
            $city->setName($name);
            $city->setPays($country);
            $manager->persist($city);
            $this->addReference('CITY_' . strtoupper($name), $city);
        }

        $manager->flush();
    }
}