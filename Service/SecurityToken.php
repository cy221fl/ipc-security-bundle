<?php

namespace IPC\SecurityBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityToken
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * SecurityTokenGenerator constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface      $session
     */
    public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session      = $session;
    }

    /**
     * @param UserInterface $user
     * @param string        $firewall
     *
     * @return $this
     */
    public function generateToken(UserInterface $user, $firewall)
    {
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $this->tokenStorage->setToken($token);
        $this->session->set('_security_' . $firewall, serialize($token));

        return $this;
    }
}
