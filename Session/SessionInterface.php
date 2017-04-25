<?php

namespace SCTV\Security\Session;

interface SessionInterface
{
    /**
     * セッションに保存します。
     *
     * @param string $key       識別キー
     * @param mixed $value      保存データ
     */
    public function set($key, $value);

    /**
     * セッションから読み込みます。
     *
     * @param string $key       識別キー
     * @param mixed $default    キーが存在しない場合に返す値
     * @return mixed
     */
    public function get($key, $default);

    /**
     * セッションから削除します。
     *
     * @param string $key       識別キー
     * @return mixed
     */
    public function remove($key);
}