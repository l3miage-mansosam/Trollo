<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: '`%env(APP_TABLE_PREFIX)%booking`')]
#[Index(name: 'IDX_User_Booking', columns: ['user_id'])]
#[Index(name: 'IDX_Session_Booking', columns: ['session_id'])]
#[ORM\UniqueConstraint(name: 'UNIQ_Booking_User_Session', columns: ['user_id', 'session_id'])]
#[UniqueEntity(fields: ['user_id', 'session_id'], message: 'Vous avez déjà effectué une réservation pour ce trajet.')]
class Booking
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private  ?Ulid $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    private ?User $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    private ?Session $session_id = null;

    /**
     * @var Collection<int, Seat>
     */
    #[ORM\OneToMany(targetEntity: Seat::class, mappedBy: 'booking_id')]
    private Collection $seats;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Date()]
    private ?\DateTimeInterface $reservation_date = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    #[Assert\GreaterThanOrEqual(value: 0)]
    private ?float $price = null;

    public function __construct()
    {
        $this->seats = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
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

    /**
     * @return Collection<int, Seat>
     */
    public function getSeats(): Collection
    {
        return $this->seats;
    }

    public function addSeat(Seat $seat): static
    {
        if (!$this->seats->contains($seat)) {
            $this->seats->add($seat);
            $seat->setBookingId($this);
        }

        return $this;
    }

    public function removeSeat(Seat $seat): static
    {
        if ($this->seats->removeElement($seat)) {
            // set the owning side to null (unless already changed)
            if ($seat->getBookingId() === $this) {
                $seat->setBookingId(null);
            }
        }

        return $this;
    }

    public function getReservationDate(): ?\DateTimeInterface
    {
        return $this->reservation_date;
    }

    public function setReservationDate(\DateTimeInterface $reservation_date): static
    {
        $this->reservation_date = $reservation_date;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
