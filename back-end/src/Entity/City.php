<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table(name: '`%env(APP_TABLE_PREFIX)%city`')]
#[ORM\UniqueConstraint(name: 'UNIQ_City_Name', columns: ['name', 'pays'])]
#[UniqueEntity(fields: ['name', 'pays'], message: 'La ville à déjà était enregistrée pour ce pays.')]
class City
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

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    private ?string $pays = null;

    /**
     * @var Collection<int, Road>
     */
    #[ORM\OneToMany(targetEntity: Road::class, mappedBy: 'start_city', orphanRemoval: true)]
    private Collection $roads;

    public function __construct()
    {
        $this->roads = new ArrayCollection();
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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection<int, Road>
     */
    public function getRoads(): Collection
    {
        return $this->roads;
    }

    public function addRoad(Road $road): static
    {
        if (!$this->roads->contains($road)) {
            $this->roads->add($road);
            $road->setStartCityId($this);
        }

        return $this;
    }

    public function removeRoad(Road $road): static
    {
        if ($this->roads->removeElement($road)) {
            // set the owning side to null (unless already changed)
            if ($road->getStartCityId() === $this) {
                $road->setStartCityId(null);
            }
        }

        return $this;
    }
}
