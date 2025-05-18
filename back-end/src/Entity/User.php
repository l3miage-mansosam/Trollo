<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`%env(APP_TABLE_PREFIX)%user`')]
#[ORM\Index(name: 'IDX_User_Role', columns: ['role_id'])]
#[ORM\UniqueConstraint(name: 'UNIQ_User_Email', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé par un autre utilisateur.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 20)]
    private ?string $last_name = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 20)]
    private ?string $first_name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    #[Assert\Length(min: 3, max: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Length(min: 6, max: 50)]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Valid()]
    private ?Role $role_id = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'user_id', orphanRemoval: true)]
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
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

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

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, nettoie-les ici
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->role_id instanceof Role) {
            $name = strtoupper($this->role_id->getName());

            if (!str_starts_with($name, 'ROLE_')) {
                $name = 'ROLE_' . $name;
            }

            $roles[] = $name;
        }

        return array_unique($roles);
    }

    public function getRoleId(): ?Role
    {
        return $this->role_id;
    }

    public function setRoleId(?Role $role_id): static
    {
        $this->role_id = $role_id;

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
            $booking->setUserId($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUserId() === $this) {
                $booking->setUserId(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
