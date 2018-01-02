<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\Console;

use CosmonovaRnD\Auth\Domain\TokenDataPersister;
use CosmonovaRnD\Auth\Http\TokenLoader;
use CosmonovaRnD\Auth\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RenewTokensCommand
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\Console
 * Cosmonova | Research & Development
 */
class RenewTokensCommand extends Command
{
    /**
     * @var \CosmonovaRnD\Auth\Domain\TokenDataPersister
     */
    private $tokenDataPersister;
    /**
     * @var \CosmonovaRnD\Auth\Http\TokenLoader
     */
    private $tokenLoader;
    /**
     * @var \CosmonovaRnD\Auth\Repository\UserRepository
     */
    private $userRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * RenewTokensCommand constructor.
     *
     * @param \CosmonovaRnD\Auth\Domain\TokenDataPersister $tokenDataPersister
     * @param \CosmonovaRnD\Auth\Http\TokenLoader          $tokenLoader
     * @param \CosmonovaRnD\Auth\Repository\UserRepository $userRepository
     * @param \Psr\Log\LoggerInterface                     $logger
     */
    public function __construct(
        TokenDataPersister $tokenDataPersister,
        TokenLoader $tokenLoader,
        UserRepository $userRepository,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->tokenDataPersister = $tokenDataPersister;
        $this->tokenLoader        = $tokenLoader;
        $this->userRepository     = $userRepository;
        $this->logger             = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('token:renew')
            ->setDescription('Renew existing access tokens using refresh tokens');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $toTime = new \DateTime('+2 minutes');
        /** @var \CosmonovaRnD\Auth\Entity\User[] $users */
        $users = $this->userRepository->createQueryBuilder('u')
            ->where('u.expires <= :time')
            ->setParameter('time', $toTime)
            ->getQuery()
            ->getResult();

        $successRenews = 0;

        foreach ($users as $user) {
            if (null !== $user->getRefreshToken()) {
                $tokenData = $this->tokenLoader->refreshTokenDataBy($user->getRefreshToken());

                if (null === $tokenData) {
                    continue;
                }

                $ok = $this->tokenDataPersister->persist($tokenData);

                if (null !== $ok) {
                    $successRenews++;
                }
            }
        }

        $output->writeln('Success renews ' . $successRenews);
    }
}
