<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_City_Name', columns: ['name', 'pays'])]
#[UniqueEntity(fields: ['name', 'pays'], message: 'La ville à déjà était enregistrée pour ce pays.')]
#[OA\Schema(
    title: 'City',
    description: 'Entité représentant une ville',
    type: 'object'
)]
class City
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[OA\Property(
        description: 'Identifiant unique de la ville (ULID)',
        type: 'string',
        example: '01H2XJWN8D8RJXPTH2FWVG6PKG'
    )]
    #[Groups(['city:show'])]
    private ?Ulid $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    #[OA\Property(
        description: 'Nom de la ville',
        type: 'string',
        maxLength: 50,
        minLength: 3,
        example: 'Paris'
    )]
    #[Groups(['city:show', 'city:create', 'city:edit'])]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    #[OA\Property(
        description: 'Pays où se situe la ville',
        type: 'string',
        maxLength: 50,
        minLength: 3,
        example: 'France'
    )]
    #[Groups(['city:show', 'city:create', 'city:edit'])]
    private ?string $pays = null;

    /**
     * @var Collection<int, Road>
     */
    #[ORM\OneToMany(targetEntity: Road::class, mappedBy: 'start_city', orphanRemoval: true)]
    #[OA\Property(
        description: 'Routes partant de cette ville',
        type: 'array',
        items: new OA\Items(ref: new Model(type: Road::class))
    )]
    #[Groups(['city:show'])]
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
            $road->setStartCity($this);
        }

        return $this;
    }

    public function removeRoad(Road $road): static
    {
        if ($this->roads->removeElement($road)) {
            // set the owning side to null (unless already changed)
            if ($road->getStartCity() === $this) {
                $road->setStartCity(null);
            }
        }

        return $this;
    }
}