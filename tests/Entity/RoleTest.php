<?php

namespace Tests\IPC\SecurityBundle\Entity;

use IPC\SecurityBundle\Entity\Role;
use IPC\SecurityBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{

    /**
     * @var Role
     */
    private $role;

    protected function setUp()
    {
        $this->role = new Role();
    }

    public function testRoleKeyMethods()
    {
        $this->role = new Role('ROLE_USER');
        $this->assertEquals('ROLE_USER', $this->role->getRoleKey());
        $this->role->setRoleKey('ROLE_ADMIN');
        $this->assertEquals('ROLE_ADMIN', $this->role->getRoleKey());
    }

    public function testGetRole()
    {
        $this->assertNull($this->role->getRole());
        $this->role->setRoleKey('ROLE_ADMIN');
        $this->assertEquals('ROLE_ADMIN', $this->role->getRole());
    }

    public function testUserMethods()
    {
        $user = new User();
        $this->assertEmpty($this->role->getUsers());
        $this->role->setRoleKey('ROLE_USER');

        $this->role->addUser($user);
        $this->assertCount(1, $this->role->getUsers());

        $this->role->removeUser($user);
        $this->assertCount(0, $this->role->getUsers());
    }
}
