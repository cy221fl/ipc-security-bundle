<?php

namespace Tests\IPC\SecurityBundle\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use IPC\SecurityBundle\Entity\User;
use IPC\SecurityBundle\Provider\DoctrineUserProvider;
use IPC\TestBundle\Tests\AbstractSymfonyTest;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class DoctrineUserProviderTest extends AbstractSymfonyTest
{

    public function testConstructorException()
    {
        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessageRegExp('/Instances of ".*" are not supported./');
        new DoctrineUserProvider($this->container->get('doctrine'), \stdClass::class);
    }

    public function testLoadUserByUsernameUsernameNotFoundException()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionMessage('Username could not be found.');
        $exception = new NoResultException();
        $managerRegistry = $this->getMockedManagerRegistry($exception);
        $provider = new DoctrineUserProvider($managerRegistry, User::class);

        $provider->loadUserByUsername('user');
    }

    public function testLoadUserByUsernameAuthenticationException()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Multiple users found by username property.');
        $exception = new NonUniqueResultException();
        $managerRegistry = $this->getMockedManagerRegistry($exception);
        $provider = new DoctrineUserProvider($managerRegistry, User::class);

        $provider->loadUserByUsername('user');
    }

    public function testLoadUserByUsername()
    {
        $user = new User();
        $managerRegistry = $this->getMockedManagerRegistry($user);
        $provider = new DoctrineUserProvider($managerRegistry, User::class);
        $this->assertEquals($user, $provider->loadUserByUsername('user'));
    }

    public function testRefreshUserException()
    {
        // create a UserInterface based user that is not based on User
        $user = $this
            ->getMockBuilder(UserInterface::class)
            ->setMethods([
                'eraseCredentials',
                'getPassword',
                'getRoles',
                'getSalt',
                'getUsername'
            ])
            ->getMock();

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessageRegExp('/Instances of ".*" are not supported./');
        $managerRegistry = $this->getMockedManagerRegistry($user);
        $provider = new DoctrineUserProvider($managerRegistry, User::class);
        $provider->refreshUser($user);
    }

    public function testRefreshUser()
    {
        $user          = new User();
        $userRefreshed = new User();

        $managerRegistry = $this->getMockedManagerRegistry($userRefreshed);
        $provider = new DoctrineUserProvider($managerRegistry, User::class);
        $this->assertEquals($userRefreshed, $provider->refreshUser($user));
    }

    public function testSupportsClass()
    {
        $provider = new DoctrineUserProvider($this->container->get('doctrine'), User::class);

        $this->assertTrue($provider->supportsClass(User::class));
        $this->assertTrue($provider->supportsClass(ExtendedUser::class));
        $this->assertFalse($provider->supportsClass(new \stdClass()));
    }

    protected function getMockedManagerRegistry(&$result)
    {
        $query = $this
            ->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSingleResult'])
            ->getMockForAbstractClass();

        $query
            ->expects($this->any())
            ->method('getSingleResult')
            ->will($this->returnCallback(function () use ($result) {
                if ($result instanceof NoResultException || $result instanceof NonUniqueResultException) {
                    throw $result;
                }
                return $result;
            }));

        $queryBuilder = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQuery', 'getRootAlias', 'setParameter'])
            ->getMock();

        $queryBuilder
            ->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $queryBuilder
            ->expects($this->any())
            ->method('getRootAlias')
            ->will($this->returnValue(''));
        $queryBuilder
            ->expects($this->any())
            ->method('setParameter')
            ->will($this->returnSelf());

        $repository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['createQueryBuilder'])
            ->getMock();

        $repository
            ->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder));

        $manager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();

        $manager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $managerRegistry = $this
            ->getMockBuilder(ManagerRegistry::class)
            ->setMethods([
                'getAliasNamespace',
                'getConnection',
                'getConnectionNames',
                'getConnections',
                'getDefaultConnectionName',
                'getDefaultManagerName',
                'getManager',
                'getManagerForClass',
                'getManagerNames',
                'getManagers',
                'getRepository',
                'resetManager'
            ])
            ->getMock();

        $managerRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($manager));

        return $managerRegistry;
    }
}

class ExtendedUser extends User {}