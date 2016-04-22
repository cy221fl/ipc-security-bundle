<?php

namespace Tests\IPC\SecurityBundle\Entity;

use IPC\SecurityBundle\Entity\User;
use IPC\SecurityBundle\Entity\Role;
use PHPUnit_Framework_TestCase;

class UserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var User
     */
    private $user;

    protected function setUp()
    {
        $this->user = new User();
    }

    public function testUsernameMethods()
    {
        $this->assertNull($this->user->getUsername());
        $this->user->setUsername('username');
        $this->assertEquals('username', $this->user->getUsername());
    }

    public function testPlainPasswordMethods()
    {
        $this->assertNull($this->user->getPlainPassword());
        $this->user->setPlainPassword('plain_password');
        $this->assertEquals('plain_password', $this->user->getPlainPassword());
    }

    public function testPasswordMethods()
    {
        $this->assertNull($this->user->getPassword());
        $this->user->setPassword('password');
        $this->assertEquals('password', $this->user->getPassword());
    }

    public function testSaltMethods()
    {
        $this->assertNull($this->user->getSalt());
        $this->user->setSalt('salt');
        $this->assertEquals('salt', $this->user->getSalt());
    }

    public function testEraseCredentialsMethods()
    {
        $this->user->setPlainPassword('plain_password');
        $this->user->setPassword('password');
        $this->user->eraseCredentials();
        $this->assertNull($this->user->getPlainPassword());
        $this->assertEquals('password', $this->user->getPassword());
    }

    public function testExpiredMethods()
    {
        $this->assertFalse($this->user->isExpired());
        $this->assertTrue($this->user->isAccountNonExpired());
        $this->user->setExpired(true);
        $this->assertTrue($this->user->isExpired());
        $this->assertFalse($this->user->isAccountNonExpired());
    }

    public function testLockedMethods()
    {
        $this->assertFalse($this->user->isLocked());
        $this->assertTrue($this->user->isAccountNonLocked());
        $this->user->setLocked(true);
        $this->assertTrue($this->user->isLocked());
        $this->assertFalse($this->user->isAccountNonLocked());
    }

    public function testEnabledMethods()
    {
        $this->assertFalse($this->user->isEnabled());
        $this->user->setEnabled(true);
        $this->assertTrue($this->user->isEnabled());
    }

    public function testCredentialsExpiredMethods()
    {
        $this->assertFalse($this->user->isCredentialsExpired());
        $this->assertTrue($this->user->isCredentialsNonExpired());
        $this->user->setCredentialsExpired(true);
        $this->assertTrue($this->user->isCredentialsExpired());
        $this->assertFalse($this->user->isCredentialsNonExpired());
    }

    public function testSerializableInterface()
    {
        $this->user
            ->setUsername('username')
            ->setPassword('password')
            ->setSalt('salt')
            ->setExpired(true)
            ->setLocked(true)
            ->setCredentialsExpired(true)
            ->setEnabled(true)
            ->addRole('ROLE_ADMIN')
            ->addRole(new Role('ROLE_USER'))
        ;
        $user = new User();
        $user->unserialize($this->user->serialize());
        $this->assertEquals($this->user, $user);
    }

    public function testRolesMethods()
    {
        $this->assertEmpty($this->user->getRoles());
        $this->user->addRole('ROLE_ADMIN');
        $this->assertArrayHasKey('ROLE_ADMIN', $this->user->getRoles());
        $this->user->addRole(new Role('ROLE_USER'));
        $this->assertArrayHasKey('ROLE_USER', $this->user->getRoles());
        $this->user->removeRole('ROLE_USER');
        $this->assertArrayNotHasKey('ROLE_USER', $this->user->getRoles());
    }

    public function testIsEqualToMethod()
    {
        $this->user
            ->setUsername('username')
            ->setPassword('password')
            ->setSalt('salt');
        $user = new User();
        $this->assertFalse($this->user->isEqualTo($user));
        $user
            ->setUsername('username')
            ->setPassword('password')
            ->setSalt('salt');
        $this->assertTrue($this->user->isEqualTo($user));
    }
}

