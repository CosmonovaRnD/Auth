<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\Repository;

use CosmonovaRnD\Auth\Entity\User;
use CosmonovaRnD\Auth\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\Repository
 * Cosmonova | Research & Development
 */
class UserRepository extends EntityRepository implements ServiceEntityRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param \CosmonovaRnD\Auth\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, $em->getClassMetadata(User::class));
    }

    /**
     * @param string $username
     *
     * @return \CosmonovaRnD\Auth\Entity\User|null
     */
    public function findByUsername(string $username): ?User
    {
        /** @var User $user */
        $user = $this->findOneBy(['username' => $username]);

        return $user;
    }
}
