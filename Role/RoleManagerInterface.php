<?php
namespace SCTV\Security\Role;

interface RoleManagerInterface
{
    /**
     * 要求ロールが付与されているかチェックを行います。
     *
     * @param array $roles      要求ロール
     * @return boolean          ロールを持っていればtrue
     */
    public function hasRole($roles);

    /**
     * ロールを付与します
     *
     * @param array|string $roles
     */
    public function setRoles($roles);

    /**
     * ロールを追加付与します。
     *
     * @param array|string $roles
     */
    public function addRoles($roles);

    /**
     * 付与されているロールを返します。
     *
     * @return array
     */
    public function getRoles();

    /**
     * 付与されているロールをクリアします。
     */
    public function clearRoles();

    /**
     * ドメイン（認証エリア識別名）を設定します。
     *
     * @param string $domain
     */
    public function setDomain($domain);
}