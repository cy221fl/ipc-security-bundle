<?php

namespace IPC\SecurityBundle\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
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
     *
     * @throws UnsupportedUserException
     */
    public function __construct(ManagerRegistry $managerRegistry, $entityClass, array $usernameProperties = ['username'])
    {
        if (!$this->supportsClass($entityClass)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $entityClass));
        } else {
            $this->entityClass = $entityClass;
        }

        $this->managerRegistry = $managerRegistry;
        $this->usernameProperties = $usernameProperties;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AuthenticationException
     */
    public function loadUserByUsername($username)
    {
        /* @var EntityRepository $repository */
        $repository = $this
            ->managerRegistry
            ->getManagerForClass($this->entityClass)
            ->getRepository($this->entityClass)
        ;
        try {
            $qb = $repository->createQueryBuilder('u');
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
            throw new AuthenticationException('Multiple users found by username property.', 0, $e);
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
        return
            $class === User::class ||
            $class instanceof User ||
            is_subclass_of($class, User::class)
        ;
    }
}
