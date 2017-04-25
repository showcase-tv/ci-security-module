<?php

namespace SCTV\Security\Auth;

interface AuthManagerInterface
{
    /**
     * ドメイン（認証エリア識別名）を設定します。
     *
     * @param string $domain
     */
    public function setDomain($domain);

    /**
     * ユーザーのサインイン状態を返します。
     *
     * @return boolean      サインイン済みならtrue
     */
    public function isAuthenticated();

    /**
     * サインインします。
     *
     * @param mixed $user   セッションに保持するユーザーオブジェクト
     */
    public function signIn($user);

    /**
     * サインアウトします。
     * 保持していたユーザーオブジェクトも破棄します。
     */
    public function signOut();

    /**
     * サインインしているユーザーオブジェクトを返します。
     *
     * @return mixed
     */
    public function getUser();
}