<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: '`%env(APP_TABLE_PREFIX)%session`')]
#[ORM\Index(name: 'IDX_Road_Session', columns: ['road_id'])]
#[ORM\Index(name: 'IDX_Bus_Session', columns: ['bus_id'])]
class Session
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private  ?Ulid $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    private ?Road $road_id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    private ?Bus $bus_id = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    #[Assert\Type(type: 'float')]
    #[Assert\GreaterThanOrEqual(value: 0)]
    private ?float $unit_price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\DateTime()]
    private ?\DateTimeInterface $departure_date = null;

    /**
     * @var Collection<int, Seat>
     */
    #[ORM\OneToMany(targetEntity: Seat::class, mappedBy: 'session_id')]
    private Collection $seats;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'session_id', orphanRemoval: true)]
    private Collection $bookings;

    public function __construct()
    {
        $this->seats = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }


    public function getRoadId(): ?Road
    {
        return $this->road_id;
    }

    public function setRoadId(?Road $road_id): static
    {
        $this->road_id = $road_id;

        return $this;
    }

    public function getBusId(): ?Bus
    {
        return $this->bus_id;
    }

    public function setBusId(?Bus $bus_id): static
    {
        $this->bus_id = $bus_id;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unit_price;
    }

    public function setUnitPrice(float $unit_price): static
    {
        $this->unit_price = $unit_price;

        return $this;
    }

    public function getDepartureDate(): ?\DateTimeInterface
    {
        return $this->departure_date;
    }

    public function setDepartureDate(\DateTimeInterface $departure_date): static
    {
        $this->departure_date = $departure_date;

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
            $seat->setSessionId($this);
        }

        return $this;
    }

    public function removeSeat(Seat $seat): static
    {
        if ($this->seats->removeElement($seat)) {
            // set the owning side to null (unless already changed)
            if ($seat->getSessionId() === $this) {
                $seat->setSessionId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setSessionId($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getSessionId() === $this) {
                $booking->setSessionId(null);
            }
        }

        return $this;
    }
}
