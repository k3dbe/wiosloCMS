<?php

namespace wiosloCMS\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use wiosloCMS\UserBundle\Model\om\BaseUser;

class User extends BaseUser implements UserInterface
{
    private $roles;

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return null;
    }

    public function getRoles($criteria = NULL, \PropelPDO $con = NULL)
    {
        if (null !== $this->roles) {

            return $this->roles;
        }

        $this->roles = ['ROLE_USER'];

        /** @var Role $role */
        foreach (parent::getRoles() as $role) {
            $this->roles[] = $role->getName();
        }

        return $this->roles;
    }

    public function hasRole($role)
    {
        $roles = $this->getRoles();

        return in_array($role, $roles);
    }

    public function eraseCredentials()
    {
        return;
    }

}
