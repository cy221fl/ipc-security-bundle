<?php

namespace IPC\SecurityBundle\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use IPC\SecurityBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DoctrineUserProvider implements UserProviderInterface
{

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var array
     */
    protected $usernameProperties;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param string $entityClass
     * @param array $usernameProperties
     */
    public function __construct($managerRegistry, $entityClass, $usernameProperties)
    {
        $this->managerRegistry = $managerRegistry;
        $this->entityClass = $entityClass;
        $this->usernameProperties = $usernameProperties;
    }

    /**
     * {@inheritdoc}
     * @throws AuthenticationException
     */
    public function loadUserByUsername($username)
    {
        /* @var EntityManager $manager */
        $manager = $this->managerRegistry->getManagerForClass($this->entityClass);

        try {
            $qb = $manager->createQueryBuilder();
            $qb->select('u, r')->leftJoin('u.roles', 'r');

            foreach ($this->usernameProperties as $property) {
                $qb->orWhere('u.' . $property . ' = :' . $property);
                $qb->setParameter($property, $username);
            }

            $user = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $notFoundException = new UsernameNotFoundException('Username could not be found.', 0, $e);
            $notFoundException->setUsername($username);
            throw $notFoundException;
        } catch (NonUniqueResultException $e) {
            throw new AuthenticationException('Multiple users with username found.', 0, $e);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UsernameNotFoundException
     * @throws AuthenticationException
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
