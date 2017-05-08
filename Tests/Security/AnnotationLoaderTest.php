<?php

namespace SCTV\Security\Security;

use Mockery as M;
use SCTV\Security\Tests\Fixtures\UserController;
use SCTV\Security\Tests\Fixtures\AdminController;

/**
 * @preserveGlobalState disabled
 * @runTestsInSeparateProcesses
 */
class AnnotationLoaderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function setUp()
    {
        M::mock('overload:SCTV\Security\Session\SessionCI', 'SCTV\Security\Session\SessionInterface')
            ->shouldReceive('get')->once()->andReturnUsing(function ($param, $default) {
                return $default;
            })->getMock();
    }

    public function tearDown()
    {
        M::close();
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
        // class, page, secure, domain, role, redirect_url
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
