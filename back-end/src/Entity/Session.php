<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Index(name: 'IDX_Road_Session', columns: ['road_id'])]
#[ORM\Index(name: 'IDX_Bus_Session', columns: ['bus_id'])]
#[ORM\Index(name: 'IDX_City_Session_start', columns: ['start_city_id'])]
#[ORM\Index(name: 'IDX_City_Session_arrived', columns: ['arrived_city_id'])]
#[OA\Schema(
    title: 'Session',
    description: 'Entité représentant une session de voyage',
    type: 'object'
)]
class Session
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[OA\Property(
        description: 'Identifiant unique de la session (ULID)',
        type: 'string',
        example: '01H2XJWN8D8RJXPTH2FWVG6PKG'
    )]
    #[Groups(['session:show'])]
    private  ?Ulid $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    #[OA\Property(
        property: 'road',
        description: 'Route associée à la session',
        type: 'string',
        format: 'ulid',
        example: '01H2XJWN8D8RJXPTH2FWVG6PKG'
    )]
    #[Groups(['session:show','road-city:show', 'session:create', 'session:edit'])]
    private ?Road $road = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    #[OA\Property(
        property: 'bus',
        description: 'Bus assigné à la session',
        type: 'string',
        format: 'ulid',
        example: '01H2XJWN8D8RJXPTH2FWVG6PKG'
    )]
    #[Groups(['session:show','bus:show', 'session:create', 'session:edit'])]
    private ?Bus $bus = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    #[Assert\Type(type: 'float')]
    #[Assert\GreaterThanOrEqual(value: 0)]
    #[Groups(['session:show', 'session:create', 'session:edit'])]
    #[OA\Property(
        property: 'unit_price',
        description: 'Prix unitaire du voyage',
        type: 'number',
        format: 'float',
        example: 35.50
    )]
    private ?float $unit_price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\DateTime()]
    #[OA\Property(
        description: 'Date et heure de départ',
        type: 'string',
        format: 'date-time',
        example: '2025-07-08T10:00:00+00:00'
    )]
    #[Groups(['session:show','session:create', 'session:edit'])]
    private ?\DateTimeInterface $departure_date = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    #[OA\Property(
        description: 'Ville de départ',
        type: 'string',
        format: 'ulid'
    )]
    #[Groups(['session-city:show', 'session:create', 'session:edit'])]
    private ?City $start_city = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    #[OA\Property(
        description: 'Ville d\'arrivée',
        type: 'string',
        format: 'ulid'
    )]
    #[Groups(['session-city:show', 'session:create', 'session:edit'])]
    private ?City $arrived_city = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank()]
    #[Assert\Time]
    #[OA\Property(
        description: 'Temps estimé du trajet',
        type: 'string',
        format: 'time',
        example: '02:30:00'
    )]
    #[Groups(['session:show', 'session:create', 'session:edit'])]
    private ?\DateTimeInterface $estimated_time = null;


    /**
     * @var Collection<int, Seat>
     */
    #[ORM\OneToMany(targetEntity: Seat::class, mappedBy: 'session')]
    private Collection $seats;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'session', orphanRemoval: true)]
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


    public function getRoad(): ?Road
    {
        return $this->road;
    }

    public function setRoad(?Road $road): static
    {
        $this->road = $road;

        return $this;
    }

    public function getBus(): ?Bus
    {
        return $this->bus;
    }

    public function setBus(?Bus $bus): static
    {
        $this->bus = $bus;

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
            $seat->setSession($this);
        }

        return $this;
    }

    public function removeSeat(Seat $seat): static
    {
        if ($this->seats->removeElement($seat)) {
            // set the owning side to null (unless already changed)
            if ($seat->getSession() === $this) {
                $seat->setSession(null);
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
            $booking->setSession($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getSession() === $this) {
                $booking->setSession(null);
            }
        }

        return $this;
    }

    public function getStartCity(): ?City
    {
        return $this->start_city;
    }

    public function setStartCity(?City $start_city): static
    {
        $this->start_city = $start_city;

        return $this;
    }

    public function getArrivedCity(): ?City
    {
        return $this->arrived_city;
    }

    public function setArrivedCity(?City $arrived_city): static
    {
        $this->arrived_city = $arrived_city;

        return $this;
    }

    public function getEstimatedTime(): ?\DateTimeInterface
    {
        return $this->estimated_time;
    }

    public function setEstimatedTime(\DateTimeInterface $estimated_time): static
    {
        $this->estimated_time = $estimated_time;

        return $this;
    }
}
