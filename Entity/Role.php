<?php

namespace IPC\SecurityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Role\RoleInterface;

class Role implements RoleInterface
{

    /**
     * @var string
     */
    protected $roleKey;

    /**
     * @var Collection
     */
    protected $users;

    /**
     * Constructor
     *
     * @param string $roleKey role
     */
    public function __construct($roleKey = null)
    {
        $this->roleKey = $roleKey;
        $this->users = new ArrayCollection();
    }

    /**
     * Set roleKey
     *
     * @param string $roleKey
     * @return $this
     */
    public function setRoleKey($roleKey)
    {
        $this->roleKey = $roleKey;
        return $this;
    }

    /**
     * Get roleKey
     *
     * @return string
     */
    public function getRoleKey()
    {
        return $this->roleKey;
    }

    /**
     * Add user
     *
     * @param User $user
     * @return $this
     */
    public function addUser($user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRole($this);
        } // no else
        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     * @return $this
     */
    public function removeUser($user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeRole($this);
        } // no else
        return $this;
    }

    /**
     * Get users
     *
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Returns the role.
     *
     * This method returns a string representation whenever possible.
     *
     * When the role cannot be represented with sufficient precision by a
     * string, it should return null.
     *
     * @return string|null A string representation of the role, or null
     */
    public function getRole()
    {
        return $this->roleKey;
    }
}