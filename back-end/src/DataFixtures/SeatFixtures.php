<?php

namespace App\DataFixtures;

use App\Entity\Seat;
use App\Entity\Session;
use App\Enum\StateSeat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeatFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $sessions = [
            SessionFixtures::SESSION_1 => 50,
            SessionFixtures::SESSION_2 => 45,
        ];

        foreach ($sessions as $sessionRef => $seatCount) {
            for ($i = 1; $i <= $seatCount; $i++) {
                $seat = new Seat();
                $seat->setSession($this->getReference($sessionRef, Session::class));
                $seat->setNumber($i);
                $seat->setState(StateSeat::AVAILABLE);
                $manager->persist($seat);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SessionFixtures::class,
        ];
    }
}