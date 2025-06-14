<?php

namespace App\Entity;

use App\Repository\RoadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: RoadRepository::class)]
#[ORM\Index(name: 'IDX_City_Road_Start', columns: ['start_city_id'])]
#[ORM\Index(name: 'IDX_City_Road_Arrived', columns: ['arrived_city_id'])]
class Road
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private  ?Ulid $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'roads')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    private ?City $start_city = null;

    #[ORM\ManyToOne(inversedBy: 'roads')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]   
    private ?City $arrived_city = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank()]
    #[Assert\Time]
    private ?\DateTimeInterface $estimated_time = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'road_id', orphanRemoval: true)]
    private Collection $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
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

    public function setEstimatedTime(?\DateTimeInterface $estimated_time): static
    {
        $this->estimated_time = $estimated_time;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setRoad($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getRoad() === $this) {
                $session->setRoad(null);
            }
        }

        return $this;
    }
}