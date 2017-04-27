<?php

namespace SCTV\Security\Security;

use SCTV\Security\Auth\AuthManagerInterface;
use SCTV\Security\Role\RoleManagerInterface;

class SecurityContext
{
    protected static $instance;
    public $secure = false;
    public $domain = 'secure';
    public $redirectUrl;
    public $allowedRoles = [];
    public $previousUrlHolder;
    public $roleManager;
    public $authManager;

    private function __construct()
    {
    }

    /**
     * getInstance
     *
     * @return SecurityContext
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new SecurityContext();
        }
        return self::$instance;
    }

    public function isAuthenticated()
    {
        return $this->authManager->isAuthenticated();
    }

    public function setSecure($secure)
    {
        $this->secure = $secure;
        return $this;
    }

    public function isSecure()
    {
        return $this->secure;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function setAllowedRoles($allowedRoles)
    {
        $this->allowedRoles = (array)$allowedRoles;
        return $this;
    }

    public function getAllowedRoles()
    {
        return $this->allowedRoles;
    }

    public function setAuthManager(AuthManagerInterface $authLogic)
    {
        $this->authManager = $authLogic;
    }

    public function getAuthManager()
    {
        return $this->authManager;
    }

    public function fixDomain()
    {
        if ($this->authManager !== null) {
            $this->authManager->setDomain($this->domain);
        }

        if ($this->previousUrlHolder !== null) {
            $this->previousUrlHolder->setUp($this->domain);
        }

        if ($this->roleManager !== null) {
            $this->roleManager->setDomain($this->domain);
        }
    }

    public function signIn($user)
    {
        $this->authManager->signIn($user);
    }

    public function signOut()
    {
        if ($this->roleManager !== null) {
            $this->roleManager->clearRoles();
        }

        $this->authManager->signOut();
    }

    public function getUser()
    {
        return $this->authManager->getUser();
    }

    public function setPreviousUrlHolder(PreviousUrlHolder $previousUrlHolder)
    {
        $this->previousUrlHolder = $previousUrlHolder;
    }

    public function getPreviousUrlHolder()
    {
        return $this->previousUrlHolder;
    }

    public function setRoleManager(RoleManagerInterface $roleManager)
    {
        $this->roleManager = $roleManager;
    }

    public function getRoleManager()
    {
        return $this->roleManager;
    }

    public function hasAllowedRoles()
    {
        return $this->roleManager->hasRole($this->allowedRoles);
    }

    public function hasRole($roles)
    {
        return $this->roleManager->hasRole($roles);
    }

    public function setRoles($roles)
    {
        $this->roleManager->setRoles($roles);
    }

    public function addRoles($roles)
    {
        $this->roleManager->addRoles($roles);
    }

    public function hasPreviousUrl()
    {
        return $this->previousUrlHolder->has();
    }

    public function setPreviousUrl($url)
    {
        $this->previousUrlHolder->set($url);
    }

    public function getPreviousUrl()
    {
        return $this->previousUrlHolder->get();
    }
}