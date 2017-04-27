<?php

namespace SCTV\Security\Auth;

use SCTV\Security\Session\SessionInterface;

class SessionAuthManager implements AuthManagerInterface
{
    const AUTHENTICATED = 'sctv_authenticated';
    const USER = 'sctv_user';

    protected $domain;
    protected $session;
    protected $user;

    /**
     * SessionAuth constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        if ($this->user === null) {
            $this->user = $this->getAttributes(self::USER);
        }
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function signIn($user)
    {
        $this->setAttributes(self::AUTHENTICATED, true);
        $this->setAttributes(self::USER, $user);
    }

    /**
     * @inheritdoc
     */
    public function signOut()
    {
        $this->setAttributes(self::AUTHENTICATED, false);
        $this->setAttributes(self::USER, null);
        $this->user = null;
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
    public function isAuthenticated()
    {
        return $this->getAttributes(self::AUTHENTICATED, false);
    }

    /**
     * 属性をセッションに保存します。
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setAttributes($key, $value)
    {
        $sessionKey = $this->domain . '.' . $key;
        $this->session->set($sessionKey, $value);
    }

    /**
     * セッションから属性を読み込みます。
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getAttributes($key, $default = null)
    {
        $sessionKey = $this->domain . '.' . $key;
        return $this->session->get($sessionKey, $default);
    }
}