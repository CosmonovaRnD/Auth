<?php
declare(strict_types=1);

namespace CosmonovaRnD\Auth\Controller;

use CosmonovaRnD\Auth\Domain\TokenDataPersister;
use CosmonovaRnD\Auth\Entity\User;
use CosmonovaRnD\Auth\Http\TokenLoader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AuthController
 *
 * @author  Aleksandr Besedin <bs@cosmonova.net>
 * @package CosmonovaRnD\Auth\Controller
 * Cosmonova | Research & Development
 */
class AuthController extends Controller
{
    /**
     * @var string
     */
    private $authUri;
    /**
     * @var string
     */
    private $clientId;

    /**
     * AuthController constructor.
     *
     * @param string $authUri
     * @param string $clientId
     */
    public function __construct(
        string $authUri,
        string $clientId
    ) {

        $this->authUri  = $authUri;
        $this->clientId = $clientId;
    }

    /**
     * @param Request                                                                             $request
     * @param \CosmonovaRnD\Auth\Http\TokenLoader                                                 $tokenLoader
     *
     * @param \Symfony\Component\Routing\RouterInterface                                          $router
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface                          $session
     * @param \CosmonovaRnD\Auth\Domain\TokenDataPersister                                        $persister
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function auth(
        Request $request,
        TokenLoader $tokenLoader,
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        TokenDataPersister $persister
    ): RedirectResponse {
        if ($request->query->has('code')) {
            $tokenData = $tokenLoader->getTokenDataBy($request->query->get('code'));

            if (null === $tokenData) {
                throw new AuthenticationException('Authentication exception');
            }

            $user = $persister->persist($tokenData);
            $this->authenticate($user, $session, $tokenStorage);

            return $this->redirect('/');
        }

        $uri = $this->authUri . '?' .
               \http_build_query([
                                     'response_type' => 'code',
                                     'client_id'     => $this->clientId,
                                     'redirect_uri'  => $router->generate('auth', [], RouterInterface::ABSOLUTE_URL)
                                 ]);

        return $this->redirect($uri);
    }

    /**
     * @param User                  $user
     * @param SessionInterface      $session
     * @param TokenStorageInterface $tokenStorage
     */
    private function authenticate(User $user, SessionInterface $session, TokenStorageInterface $tokenStorage): void
    {
        $firewall = 'main';
        $token    = new UsernamePasswordToken($user, '', $firewall, $user->getRoles());
        $tokenStorage->setToken($token);
        $session->set('_security_' . $firewall, \serialize($token));
    }
}