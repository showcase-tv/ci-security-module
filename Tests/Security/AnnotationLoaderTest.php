<?php

namespace SCTV\Security\Security;

use Mockery as M;
use SCTV\Security\Security\AnnotationLoader;
use SCTV\Security\Session\SessionInterface;
use SCTV\Security\Tests\Fixtures\UserController;
use SCTV\Security\Tests\Fixtures\AdminController;

function get_instance()
{
    return AnnotationLoaderTest::$function->get_instance();
}

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AnnotationLoaderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public $CI;
    public static $function;

    public function setUp()
    {
        $session = M::mock('overload:SCTV\Security\Session\SessionCI', 'SCTV\Security\Session\SessionInterface');
        $session->shouldReceive('get')->andReturnUsing(function ($param, $default) {
            return $default;
        });
//        $session->shouldReceive('set');
        //M::mock('overload:SCTV\Security\Security\SessionAuthManager');
    }

    public function ret($param, $default)
    {
        return $default;
    }

    /**
     * @dataProvider getAnnotationData
     */
    public function testLoad($controller, $method, $secure, $domain, $role, $redirect_url)
    {
        $context = SecurityContext::getInstance();

        $_class = new \ReflectionClass($controller);
        $_method = $_class->getMethod($method);

        $loader = new AnnotationLoader();
        $loader->load($context, $_class, $_method);

        $this->assertEquals($secure, $context->isSecure());
        $this->assertEquals($role, $context->getAllowedRoles());
        $this->assertEquals($domain, $context->getDomain());
        $this->assertEquals($redirect_url, $context->getRedirectUrl());
    }

    public function getAnnotationData()
    {
        // class, secure, role, redirect_url
        return [
            [new UserController(), 'securePage', true, 'user', ['normal'], '/signin'],
            [new UserController(), 'otherRedirectPage', true, 'secure', ['normal'], '/other_page'],
            [new UserController(), 'otherRolePage', true, 'user', ['super'], '/signin'],
            [new UserController(), 'nonRolePage', true, 'user', [], '/signin'],
            [new UserController(), 'publicPage', false, 'user', [], '/signin'],
            [new UserController(), 'publicPage', false, 'user', [], '/signin'],
            [new AdminController(), 'securePage', true, 'admin', ['normal'], '/signin'],
            [new AdminController(), 'secureSuperAdminPage', true, 'secure', ['super_admin'], '/super_signin'],
            [new AdminController(), 'publicPage', false, 'admin', [], '/signin'],
        ];
    }
}
