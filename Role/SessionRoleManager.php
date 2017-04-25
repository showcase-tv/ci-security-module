<?php

namespace SCTV\Security\Role;

use SCTV\Security\Session\SessionInterface;

class SessionRoleManager implements RoleManagerInterface
{
    const ROLE = 'sctv_roles';

    protected $roles;
    protected $domain;
    protected $session;

    /**
     * SessionRoleManager constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @inheritdoc
     */
    public function addRoles($roles)
    {
        $this->setRoles(
            array_unique(array_merge($this->getRoles(), (array)$roles), SORT_STRING)
        );
    }

    /**
     * @inheritdoc
     */
    public function setRoles($roles)
    {
        $this->roles = (array)$roles;
        $this->setAttributes($this->roles);
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            $this->roles = $this->getAttributes();
        }
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function hasRole($role)
    {
        $roles = (array)$role;

        if (count($roles) === 0) {
            return true;
        }

        $permission = $this->getRoles();
        if (count(array_intersect($roles, $permission)) === 0) {
            return false;
        };
        return true;
    }

    /**
     * @inheritdoc
     */
    public function clearRoles()
    {
        $this->roles = [];
        $this->session->set($this->domain . '.' . self::ROLE, $this->roles);
    }

    /**
     * 属性をセッションに保存します。
     *
     * @param mixed $value
     */
    protected function setAttributes($value)
    {
        $this->session->set($this->domain . '.' . self::ROLE, $value);
    }

    /**
     * セッションから属性を読み込みます。
     *
     * @return mixed
     */
    protected function getAttributes()
    {
        return $this->session->get($this->domain . '.' . self::ROLE, []);
    }
}