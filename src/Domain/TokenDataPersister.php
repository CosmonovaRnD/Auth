<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\Domain;

use CosmonovaRnD\Auth\Dto\TokenData;
use CosmonovaRnD\Auth\Entity\User;
use CosmonovaRnD\Auth\ORM\EntityManager;
use CosmonovaRnD\JWT\Parser\Parser;
use Psr\Log\LoggerInterface;

/**
 * Class TokenDataPersister
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\Domain
 * Cosmonova | Research & Development
 */
class TokenDataPersister
{
    /**
     * @var \CosmonovaRnD\Auth\ORM\EntityManager
     */
    private $em;
    /**
     * @var \CosmonovaRnD\JWT\Parser\Parser
     */
    private $parser;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * TokenDataPersister constructor.
     *
     * @param \CosmonovaRnD\Auth\ORM\EntityManager $em
     * @param \CosmonovaRnD\JWT\Parser\Parser      $parser
     * @param \Psr\Log\LoggerInterface             $logger
     */
    public function __construct(EntityManager $em, Parser $parser, LoggerInterface $logger)
    {
        $this->em     = $em;
        $this->parser = $parser;
        $this->logger = $logger;
    }

    /**
     * @param \CosmonovaRnD\Auth\Dto\TokenData $tokenData
     *
     * @return \CosmonovaRnD\Auth\Entity\User
     */
    public function persist(TokenData $tokenData): User
    {
        $repository = $this->em->getRepository(User::class);

        $accessToken = $this->parser->parse($tokenData->accessToken);

        $user = $repository->findByUsername($accessToken->user());

        if (null === $user) {
            $user = new User();
        }

        try {
            $user->setExpires(\DateTime::createFromFormat('U', (string)$accessToken->expires()->format('U')));
            $user->setUsername($accessToken->user());
            $user->setRoles($accessToken->roles());
            $user->setAccessToken($tokenData->accessToken);
            $user->setRefreshToken($tokenData->refreshToken);

            $this->em->persist($user);
            $this->em->flush();
        } catch (\Throwable $throwable) {
            $this->logger->error('Error when saving user: ' . $throwable->getMessage());
        }

        return $user;
    }
}
