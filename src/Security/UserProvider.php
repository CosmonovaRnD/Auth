<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\Security;

use CosmonovaRnD\Auth\Entity\User;
use CosmonovaRnD\Auth\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\Security
 * Cosmonova | Research & Development
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var \CosmonovaRnD\Auth\Repository\UserRepository
     */
    private $repository;

    /**
     * UserProvider constructor.
     *
     * @param \CosmonovaRnD\Auth\Repository\UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $username
     *
     * @return \CosmonovaRnD\Auth\Entity\User
     */
    public function loadUserByUsername($username): User
    {
        $user = $this->repository->findByUsername($username);

        if (null === $user) {
            throw new UsernameNotFoundException('User not found');
        }

        return $user;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return \CosmonovaRnD\Auth\Entity\User
     */
    public function refreshUser(UserInterface $user): User
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return $class === User::class;
    }

}
