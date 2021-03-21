<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Entity\User;
use Manager\UserRoleManager;

class Authentification
{
    protected $authentificated;
    protected $roles;
    protected $user;
    protected $manager;

    public function __construct($bdd)
    {
        $this->manager = new Managers($bdd);
        $this->setAuthentificated();
    }

    public function isAuthentificated()
    {
        if ($this->authentificated === true) {
            return true;
        }    
        return false;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles()
    {
        $flags = [
            'INNER JOIN' => [
                'table' => 'role',
                'sndTable' => 'userRole',
                'firstTag' => 'id',
                'sndTag' => 'role'
                ]
            ];
            
        $roles = $this->manager->findBy('userRole', ['WHERE' => "user = {$this->user->getId()}"], $flags);
        
        if (false === $roles) {
            return $this;
        }

        foreach ($roles as $role) {
            $this->roles[$role->getRole()->getName()] = [
                'create' => $role->getRole()->getCreate(),
                'update' => $role->getRole()->getUpdate(),
                'delete' => $role->getRole()->getDelete()
            ];
        }
        
        return $this;
    }

    public function isAdmin()
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }

    public function hasRole($role)
    {
        if (empty($this->roles[$role])) {
            return false;
        } else {
            return true;
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    private function setUser($user)
    {
        $this->user = $user;
    }

    private function setAuthentificated()
    {
        if (isset($_SESSION['user'])) {
            $this->authentificated = true;
            $this->setUser($_SESSION['user']);
            $this->setRoles();
        }
    }
}
