<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    public const BOOKING_1 = 'BOOKING_1';
    public const BOOKING_2 = 'BOOKING_2';

    public function load(ObjectManager $manager): void
    {
        // Booking 1
        $booking1 = new Booking();
        $booking1->setUser($this->getReference('USER_CLIENT', User::class)); // Client utilisateur
        $booking1->setSession($this->getReference('SESSION_1', Session::class)); // Session 1
        $booking1->setReservationDate(new \DateTime('+2 days')); // Réservation dans 2 jours
        $booking1->setPrice(35.00); // Tarification de la réservation
        $manager->persist($booking1);
        $this->addReference(self::BOOKING_1, $booking1); // Ajout de référence pour d'autres tests

        // Booking 2
        $booking2 = new Booking();
        $booking2->setUser($this->getReference('USER_ADMIN', User::class)); // Admin utilisateur
        $booking2->setSession($this->getReference('SESSION_2', Session::class)); // Session 2
        $booking2->setReservationDate(new \DateTime('+3 days')); // Réservation dans 3 jours
        $booking2->setPrice(45.00); // Tarification de la réservation
        $manager->persist($booking2);
        $this->addReference(self::BOOKING_2, $booking2); // Ajout de référence pour d'autres tests

        // Flush des données
        $manager->flush();
    }

    /**
     * Dépendances nécessaires pour BookingFixtures
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,  // Dépend de l'utilisateur
            SessionFixtures::class, // Dépend des sessions
        ];
    }
}
