<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

final class UserProvider implements UserProviderInterface
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): User
    {
        assert($user instanceof User);

        if (null === $reloadedUser = $this->findOneUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf(
                'User with ID "%s" could not be reloaded.',
                $user->getId()
            ));
        }

        return $reloadedUser;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByIdentifier(string $email): User
    {
        $user = $this->findOneUserBy(['email' => $email]);

        if (!$user) {
            throw new UsernameNotFoundException(
                sprintf(
                    'User with "%s" email does not exist.',
                    $email
                )
            );
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    private function findOneUserBy(array $options): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy($options);
    }
}