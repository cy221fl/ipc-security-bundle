<?php

namespace IPC\SecurityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Serializable;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements AdvancedUserInterface, EquatableInterface, Serializable
{

    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Plain password
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Salt
     *
     * @var string
     */
    protected $salt;

    /**
     * Roles
     *
     * @var Collection|RoleInterface[]
     */
    protected $roles;

    /**
     * User expired
     *
     * @var bool
     */
    protected $expired;

    /**
     * User locked
     *
     * @var bool
     */
    protected $locked;

    /**
     * User enabled
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Credentials expired
     *
     * @var bool
     */
    protected $credentialsExpired;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->expired = false;
        $this->locked = false;
        $this->enabled = false;
        $this->credentialsExpired = false;
        $this->plainPassword = null;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return RoleInterface[] The user roles
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @param RoleInterface|string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            $role = new Role($role);
        } // no else

        return $this->roles->containsKey($role->getRole());
    }

    /**
     * @param Role | string $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        if (is_string($role)) {
            $role = new Role($role);
        } // no else
        if (!$this->hasRole($role)) {
            $this->roles->add($role);
            $role->addUser($this);
        } // no else
        return $this;
    }

    /**
     * @param RoleInterface|string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            $role = new Role($role);
        } // no else
        $criteria = Criteria::create()->where(Criteria::expr()->eq('role', $role->getRole()));
        $roles = new ArrayCollection($this->getRoles());
        return !$roles->matching($criteria)->isEmpty();
    }

    /**
     * @param RoleInterface|string $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = new Role($role);
        }
        $criteria = Criteria::create()->where(Criteria::expr()->eq('role', $role->getRole()));
        $roles = new ArrayCollection($this->getRoles());
        /* @var $userRole Role */
        foreach ($roles->matching($criteria) as $userRole) {
            $roles->removeElement($userRole);
            $userRole->removeUser($this);
        } // no else
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param string $plainPassword
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return !$this->isExpired();
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired;
    }

    /**
     * @param bool $expired
     * @return $this
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
        return $this;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return !$this->isLocked();
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return $this
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return !$this->isCredentialsExpired();
    }

    /**
     * @return bool
     */
    public function isCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * @param bool $credentialsExpired
     * @return $this
     */
    public function setCredentialsExpired($credentialsExpired)
    {
        $this->credentialsExpired = $credentialsExpired;
        return $this;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->username,
            $this->password,
            $this->salt,
            $this->locked,
            $this->expired,
            $this->credentialsExpired,
            $this->enabled,
            $this->roles->toArray()
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->username,
            $this->password,
            $this->salt,
            $this->locked,
            $this->expired,
            $this->credentialsExpired,
            $this->enabled,
            $roles
        ) = unserialize($serialized);
        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        $userKey = implode('', [
            $user->getUsername(),
            $user->getPassword(),
            $user->getSalt(),
        ]);
        $selfKey = implode('', [
            $this->getUsername(),
            $this->getPassword(),
            $this->getSalt(),
        ]);
        return $userKey === $selfKey;
    }
}
