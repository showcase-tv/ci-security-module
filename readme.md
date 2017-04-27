[![Dependency Status](https://www.versioneye.com/user/projects/58feffdb6ac171426c4147e3/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/58feffdb6ac171426c4147e3) [![Total Downloads](https://poser.pugx.org/sctv/security-module/downloads)](https://packagist.org/packages/sctv/security-module) [![Build Status](https://travis-ci.org/showcase-tv/ci-security-module.svg?branch=master)](https://travis-ci.org/showcase-tv/ci-security-module) [![Coverage Status](https://coveralls.io/repos/github/showcase-tv/ci-security-module/badge.svg?branch=master)](https://coveralls.io/github/showcase-tv/ci-security-module?branch=master) [![Code Climate](https://codeclimate.com/github/showcase-tv/ci-security-module/badges/gpa.svg)](https://codeclimate.com/github/showcase-tv/ci-security-module)
# SCTV Security Module

## 概要

CodeIgniterに簡単に認証機能を追加できるモジュールです。
認証状態に応じてルーティング制御を行います。
サインイン、サインアウトは開発者が明示的に行う必要があります。

## インストール方法

### Composer

`composer.json`に下記を追記します。
```json
{
    "require": {
        "sctv/security-module": "*"
    }
}
```

### CodeIgniter

#### config.phpの変更

`Composer`の`autoload`を有効にします。

```php
$config['composer_autoload'] = TRUE;
```

`hooks`を有効にします。

```php
$config['enable_hooks'] = TRUE;
```

`application/config/hooks.php`に下記を追記します。

```php
$hook['post_controller_constructor'][] = function() {
    $listener = new \SCTV\Security\Hook\AuthListener();
    $listener->postControllerConstructor();
};
```

## 使い方

アクセス制限したいコントローラーに`@Security`アノテーションを設定します。
`@Security`アノテーションが設定されたコントローラーには、非サインイン状態ではアクセスができなくなります。

アノテーションはクラス、メソッドどちらにも指定が可能で、親クラスに設定したアノテーションは子クラスにも継承されます。
アノテーションは親クラス => 子クラス => メソッドの順で評価され、メソッドのアノテーションが最も優先されます。

### アノテーション

#### `@SecurityConfig`

認証制御の設定を行います。

##### パラメータ

|パラメータ  |型  |初期値 | |
|---|---|---|---|
|redirect_url  |string  ||非サインインユーザーが、認証の必要なコントローラーにアクセスした際のリダイレクト先を指定します。  |
|domain  |string  |secure |認証状態は`domain`別に管理されます。 そのため、`domain`を異なるものにすることで、ユーザー専用画面、管理者専用画面等、認証エリアを分けることが可能です。|


##### 例：

```php
/**
 * @SecurityConfig(redirect_url="/signin", domain="user_area")
 */
class home extends CI_Controller
{
    /**
     * @Security
     **/
    function createUser()
    {
        /* 非サインイン状態のユーザーは/signinにリダイレクトされる */
    }
}
```


#### `@Security`

認証制御のON/OFF、Roleの設定を行います。

##### パラメータ

|パラメータ  |型  |初期値 |  |
|---|---|---|---|
|enabled  |boolean  |true |`false`を指定したコントローラーは、認証状態にかかわらずアクセス可能になります。|
|roles  |array  ||そのコントローラーのアクセスに必要な権限を配列で指定します。権限を持たないユーザーはアクセスできなくなります。|


##### 例：

```php
/**
 * @SecurityConfig(redirect_url="/signin")
 **/
class home extends CI_Controller
{
    /**
     * @Security(role={"CREATE","DEV"})
     **/
    function createUser()
    {
        /* CREATEまたはDEVロールを持つユーザーのみアクセス可能。 */
    }
}
```

#### アノテーションの継承

##### 例：
```php
/**
 * @SecurityConfig(redirect_url="/signin")
 * @Security
 **/
class home extends CI_Controller
{
    function createUser()
    {
        /* クラスに@Seurityが設定されているので、非サインインユーザーはこのメソッドにアクセスできない。 */
    }
    
    /**
     * @Security(enabled=false)
     **/
    function readUser()
    {
        /* メソッドに設定された@Securityが、クラスの@Securityのenabledパラメータより優先されるので、 */
        /* このメソッドは非サインインユーザーもアクセスできる。 */
    }
    
    /**
     * @Security(roles={"DEV"})
     **/
     function writeUesr()
     {
        /* メソッドの@Securityがクラスの@Securityのrolesパラメータより優先されるので、 */
        /* このメソッドへは、サインイン状態かつDEVロールを持つユーザーでなければアクセスできない。 */
     }
}
```

#### セキュリティコンテキスト

このインスタンスは、各種認証情報の操作、参照機能を提供します。

##### サインインする。

```php
\SCTV\Security\Security\SecurityContext::getInstance()->signIn($user);
```
##### ユーザーにロールを付与する。

```php
// ロールを設定する roles = ['hoge']になります。
\SCTV\Security\Security\SecurityContext::getInstance()->setRoles(['hoge']);

// 追加もできます　roles = ['hoge','moge']になります。
\SCTV\Security\Security\SecurityContext::getInstance()->addRoles(['moge']);
```

##### ユーザーにロールが付与されているかチェックする。

```php
if(\SCTV\Security\Security\SecurityContext::getInstance()->hasRole(['hoge'])) {
    //hogeロールを持っている場合の処理
}
```

##### ユーザーを取得する。

```php
$user = \SCTV\Security\Security\SecurityContext::getInstance()->getUser();
```

##### サインアウトする。

```php
\SCTV\Security\Security\SecurityContext::getInstance()->signOut();

$user = \SCTV\Security\Security\SecurityContext::getInstance()->getUser();
/* $user === null */

$isAuth = \SCTV\Security\Security\SecurityContext::getInstance()->isAuthenticated();
/* $isAuth === false  */
```

##### 認証状態を確認する

```php
$securityContext = \SCTV\Security\Security\SecurityContext::getInstance();
if ($securityContext->isAuthentiated()) {
    //認証済み
} else {
    //未認証
}
```

##### リダイレクト前のURLを取得する。

```php
$securityContext = \SCTV\Security\Security\SecurityContext::getInstance();
if ($securityContext->hasPreviousUrl()) {
    $url = $securityContext->getPreviousUrl();
}
```


### 使用例

```php
/**
 * 認証デモコントローラー
 *
 * @SecurityConfig(redirect_url="/auth/signin", domain="user_area")
 **/
class Home extends CI_Controller
{
    /**
     * @Security
     **/
    public function index()
    {
        $this->load->view('home');
    }
}


/**
 * ユーザー認証コントローラー
 *
 * @SecurityConfig(domain="user_area")
 **/
class Auth extends CI_Controller
{
    /**
     * サインイン
     **/
    public function signin()
    {
        $security = \SCTV\Security\Security\SecurityContext::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $userModel->search($user);
            $security->signIn($user);

            $url = '/';
            if ($security->hasPreviousUrl()) {
                $url = '/' . $security->getPreviousUrl();
            }

            header("location: {$url}");
            return;
        }

        $this->load->view('signin');
    }

    /**
     * サインアウト
     **/
    public function signout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            \SCTV\Security\Security\SecurityContext::getInstance()->signOut();
            header('location: /auth/signin');
            return;
        }

        $this->load->view('signout');
    }
}
```
