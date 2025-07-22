<?php

namespace App\Entity;

use App\Repository\BusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: BusRepository::class)]
#[ORM\UniqueConstraint(name: 'UQ_Bus_Immatriculation', columns: ['immatriculation'])]
#[UniqueEntity(fields: ['immatriculation'], message: 'L\'immatriculation est utilisée par un autre bus')]
class Bus
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private  ?Ulid $id = null;


    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 20)]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $model = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\GreaterThanOrEqual(value: 1)]
    private ?int $capacity = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'bus_id', orphanRemoval: true)]
    private Collection $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

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
            $session->setBus($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getBus() === $this) {
                $session->setBus(null);
            }
        }

        return $this;
    }
}