<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(name: 'IDX_User_Role', columns: ['role_id'])]
#[ORM\UniqueConstraint(name: 'UNIQ_User_Email', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé par un autre utilisateur.')]
#[OA\Schema(
    title: 'User',
    description: 'Entité représentant un utilisateur du système',
    type: 'object'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[OA\Property(
        description: 'Identifiant unique de l\'utilisateur (ULID)',
        type: 'string',
        example: '01H2XJWN8D8RJXPTH2FWVG6PKG'
    )]
    #[Groups(['user:show'])]
    private ?Ulid $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 20)]
    #[OA\Property(
        description: 'Nom de famille de l\'utilisateur',
        type: 'string',
        maxLength: 20,
        minLength: 3,
        example: 'Dupont'
    )]
    #[Groups(['user:show', 'user:create', 'user:edit'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 20)]
    #[OA\Property(
        description: 'Prénom de l\'utilisateur',
        type: 'string',
        maxLength: 20,
        minLength: 3,
        example: 'Jean'
    )]
    #[Groups(['user:show', 'user:create', 'user:edit'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    #[Assert\Length(min: 3, max: 180)]
    #[OA\Property(
        description: 'Adresse email de l\'utilisateur (unique)',
        type: 'string',
        format: 'email',
        maxLength: 180,
        example: 'jean.dupont@example.com'
    )]
    #[Groups(['user:show', 'user:create', 'user:edit'])]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 6, max: 50)]
    #[OA\Property(
        description: 'Mot de passe de l\'utilisateur (haché)',
        type: 'string',
        format: 'password',
        maxLength: 50,
        minLength: 6
    )]
    #[Groups(['user:create', 'user:edit'])]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    #[OA\Property(
        ref: new Model(type: Role::class),
        description: 'Rôle de l\'utilisateur',
        type: 'integer',
    )]
    #[Groups(['user:show', 'user:create', 'user:edit'])]
    private ?Role $role = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'user', orphanRemoval: true)]
    #[OA\Property(
        description: 'Liste des réservations de l\'utilisateur',
        type: 'array',
        items: new OA\Items(ref: new Model(type: Booking::class))
    )]
    #[Groups(['user:show'])]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
    */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, nettoie-les ici
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->role instanceof Role) {
            $name = strtoupper($this->role->getName());

            if (!str_starts_with($name, 'ROLE_')) {
                $name = 'ROLE_' . $name;
            }

            $roles[] = $name;
        }

        return array_unique($roles);
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

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
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}