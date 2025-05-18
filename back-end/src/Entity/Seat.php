<?php

namespace App\Entity;

use App\Enum\StateSeat;
use App\Repository\SeatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeatRepository::class)]
#[ORM\Table(name: '`%env(APP_TABLE_PREFIX)%seat`')]
#[ORM\Index(name: 'IDX_Session_Seat', columns: ['session_id'])]
#[ORM\Index(name: 'IDX_Booking_Seat', columns: ['booking_id'])]
#[ORM\UniqueConstraint(name: 'UNIQ_Seat_Number_Session', columns: ['number', 'session_id'])]
#[UniqueEntity(fields: ['number', 'session_id'], message: 'Ce numéro de siège existe déjà pour cette session.')]
class Seat
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private  ?Ulid $id = null;

    #[ORM\ManyToOne(inversedBy: 'seats')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    private ?Session $session_id = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Positive()]
    #[Assert\Type(type: 'int')]
    #[Assert\GreaterThanOrEqual(value: 0)]
    private ?int $number = null;

    #[ORM\Column(enumType: StateSeat::class)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private ?StateSeat $state = null;

    #[ORM\ManyToOne(inversedBy: 'seats')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid()]
    private ?Booking $booking_id = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getSessionId(): ?Session
    {
        return $this->session_id;
    }

    public function setSessionId(?Session $session_id): static
    {
        $this->session_id = $session_id;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getState(): ?StateSeat
    {
        return $this->state;
    }

    public function setState(StateSeat $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getBookingId(): ?Booking
    {
        return $this->booking_id;
    }

    public function setBookingId(?Booking $booking_id): static
    {
        $this->booking_id = $booking_id;

        return $this;
    }
}
