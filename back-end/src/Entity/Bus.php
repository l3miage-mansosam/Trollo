<?php

namespace App\Entity;

use App\Repository\BusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Ulid;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: BusRepository::class)]
#[ORM\UniqueConstraint(name: 'UQ_Bus_Immatriculation', columns: ['immatriculation'])]
#[UniqueEntity(fields: ['immatriculation'], message: 'L\'immatriculation est utilisée par un autre bus')]
#[OA\Schema(
    title: 'Bus',
    description: 'Entité représentant un Bus',
    type: 'object'
)]
class Bus
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[OA\Property(
        description: 'Identifiant unique du Bus (ULID)',
        type: 'string',
        example: '01H2XJWN8D8RJXPTH2FWVG6PKG'
    )]
    #[Groups(['bus:show'])]
    private  ?Ulid $id = null;


    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    #[OA\Property(
        description: 'Nom du bus',
        type: 'string',
        maxLength: 50,
        minLength: 3,
        example: 'TransIsère'
    )]
    #[Groups(['bus:show', 'bus:create', 'bus:edit'])]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 20)]
    #[OA\Property(
        description: 'Immatricule du bus',
        type: 'string',
        maxLength: 20,
        minLength: 3,
        example: 'BD-450-DC'
    )]
    #[Groups(['bus:show', 'bus:create', 'bus:edit'])]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    #[OA\Property(
        description: 'Model du bus',
        type: 'string',
        maxLength: 50,
        minLength: 3,
        example: 'Renault'
    )]
    #[Groups(['bus:show', 'bus:create', 'bus:edit'])]
    private ?string $model = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\GreaterThanOrEqual(value: 1)]
    #[OA\Property(
        description: 'Capacité du bus',
        type: 'number',
        example: 50
    )]
    #[Groups(['bus:show', 'bus:create', 'bus:edit'])]
    private ?int $capacity = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'bus', orphanRemoval: true)]
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