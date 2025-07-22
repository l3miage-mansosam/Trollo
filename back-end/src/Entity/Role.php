<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_Role_Name', columns: ['name'])]
#[UniqueEntity(fields: ['name'], message: 'Ce rôle est déjà existant')]
#[OA\Schema(
    title: 'Role',
    description: 'Entité représentant un rôle utilisateur',
    type: 'object'
)]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[OA\Property(
        description: 'Identifiant unique du rôle (ULID)',
        type: 'string',
        example: '01H2XJWN8D8RJXPTH2FWVG6PKG'
    )]
    #[Groups(['role:show'])]
    private ?Ulid $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 50)]
    #[OA\Property(
        description: 'Nom du rôle',
        type: 'string',
        maxLength: 50,
        minLength: 3,
        example: 'ADMIN'
    )]
    #[Groups(['role:show', 'role:create', 'role:edit'])]
    private ?string $name = null;

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

    public function __toString(): string
    {
        return (string) $this->name;
    }
}