<?php

namespace IPC\SecurityBundle\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use IPC\SecurityBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DoctrineUserProvider implements UserProviderInterface
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @param ObjectManager $objectManager
     * @param string $entityName
     */
    public function __construct($objectManager, $entityName)
    {
        $this->objectManager = $objectManager;
        $this->entityName = $entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->objectManager
            ->getRepository($this->entityName)
            ->findOneBy(['username' => $username])
        ;

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Unable to find user "%s".', $username));
        } // no else

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        } // no else

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class instanceof User;
    }
}
