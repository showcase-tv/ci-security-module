<?php

namespace SCTV\Security\Security;

use SCTV\Security\Session\SessionInterface;

class PreviousUrlHolder
{
    const PREVIOUSURL = 'sctv_previous_url';

    public $session;
    public $domain;
    public $url;

    /**
     * PreviousUrlHolder constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * 初期化と初期値設定を行います。
     *
     * @param string $domain    ドメイン（認証エリア識別名）
     */
    public function setUp($domain)
    {
        $this->domain = $domain;
        $this->url = $this->session->get($this->domain.'.'.self::PREVIOUSURL, null);
    }

    /**
     * URLの保存状態を返します。
     *
     * @return bool     保持していればtrue
     */
    public function has()
    {
        return isset($this->url);
    }

    /**
     * URLを返します。
     *
     * 保存していたURLは破棄されます。
     * 保存されていない場合はnullが返ります。
     *
     * @return string|null
     */
    public function get()
    {
        $this->session->remove($this->domain.'.'.self::PREVIOUSURL);
        return $this->url;
    }

    /**
     * URLを保存します。
     *
     * @param string $url
     */
    public function set($url)
    {
        $this->url = $url;
        $this->session->set($this->domain.'.'.self::PREVIOUSURL, $url);
    }

}