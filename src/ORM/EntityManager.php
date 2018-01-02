<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\ORM;

use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class EntityManager
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\ORM
 * Cosmonova | Research & Development
 */
class EntityManager extends EntityManagerDecorator
{
    /**
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $managerRegistry
     */
    public function __construct(RegistryInterface $managerRegistry)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $managerRegistry->getManager('auth');

        parent::__construct($em);
    }
}
