<?php

namespace SCTV\Security\Security;

use Mockery as M;

class SecurityContextTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var  SecurityContext */
    private $context;

    public function setUp()
    {
        $this->context = SecurityContext::getInstance();
        $this->context->roleManager = M::mock();
        $this->context->previousUrlHolder = M::mock();
        $this->context->authManager = M::mock();
    }

    public function testCallIsAuthenticated()
    {
        $this->context->authManager->shouldReceive('isAuthenticated')->once();
        $this->context->isAuthenticated();
    }

    public function testSetSecure()
    {
        $this->context->setSecure(true);
        $this->assertTrue($this->context->secure);
    }

    public function testIsSecure()
    {
        $this->assertEquals($this->context->secure, $this->context->isSecure());
    }

    public function testSetDomain()
    {
        $this->context->setDomain('test_domain');
        $this->assertEquals('test_domain', $this->context->domain);
    }

    public function testGetDomain()
    {
        $this->context->domain = 'test_domain';
        $this->assertEquals('test_domain', $this->context->getDomain());
    }

    public function testSetRedirectUrl()
    {
        $this->context->setRedirectUrl('redirect_url');
        $this->assertEquals('redirect_url', $this->context->redirectUrl);
    }

    public function testGetRedirectUrl()
    {
        $this->context->redirectUrl = 'redirect_url';
        $this->assertEquals('redirect_url', $this->context->getRedirectUrl());
    }

    public function testSetAllowedRoles()
    {
        $this->context->setAllowedRoles('role1');
        $this->assertEquals(['role1'], $this->context->allowedRoles);
    }

    public function testGetAllowdRoles()
    {
        $this->context->allowedRoles = ['role1'];
        $this->assertEquals(['role1'], $this->context->getAllowedRoles());
    }

    public function testSetAuthManager()
    {
        $mock = M::mock('SCTV\Security\Auth\AuthManagerInterface');
        $this->context->setAuthManager($mock);
        $this->assertEquals($mock, $this->context->authManager);
    }

    public function testGetAuthManager()
    {
        $this->context->authManager = M::mock('SCTV\Security\Auth\AuthManagerInterface');
        $this->assertEquals($this->context->authManager, $this->context->getAuthManager());
    }

    public function testFixDomain()
    {
        $this->context->domain = 'test_domain';
        $this->context->authManager = M::mock()->shouldReceive('setDomain')->with('test_domain')->once()->getMock();
        $this->context->roleManager = M::mock()->shouldReceive('setDomain')->with('test_domain')->once()->getMock();
        $this->context->previousUrlHolder = M::mock()->shouldReceive('setUp')->with('test_domain')->once()->getMock();
        $this->context->fixDomain();
    }

    public function testSignIn()
    {
        $this->context->authManager = M::mock()->shouldReceive('signIn')->with(['user_data'])->once()->getMock();
        $this->context->signIn(['user_data']);
    }

    public function testSignOut()
    {
        $this->context->authManager = M::mock()->shouldReceive('signOut')->once()->getMock();
        $this->context->roleManager = M::mock()->shouldReceive('clearRoles')->once()->getMock();
        $this->context->signOut();
    }

    public function testGetUser()
    {
        $this->context->authManager = M::mock()->shouldReceive('getUser')->once()->getMock();
        $this->context->getUser();
    }

    public function testSetPreviousUrlHolder()
    {
        $mock = M::mock('SCTV\Security\Security\PreviousUrlHolder');
        $this->context->setPreviousUrlHolder($mock);
        $this->assertEquals($mock, $this->context->previousUrlHolder);
    }

    public function testGetPreviousUrlHolder()
    {
        $this->context->previousUrlHolder = M::mock('SCTV\Security\Security\PreviousUrlHolder');
        $this->assertEquals($this->context->previousUrlHolder, $this->context->getPreviousUrlHolder());
    }

    public function testSetRoleManager()
    {
        $mock = M::mock('SCTV\Security\Role\RoleManagerInterface');
        $this->context->setRoleManager($mock);
        $this->assertEquals($mock, $this->context->roleManager);
    }

    public function testGetRoleManager()
    {
        $this->context->roleManager = M::mock('SCTV\Security\Role\RoleManagerInterface');
        $this->assertEquals($this->context->roleManager, $this->context->getRoleManager());
    }

    public function testHasAllowedRoles()
    {
        $this->context->allowedRoles = ['role1'];
        $this->context->roleManager = M::mock()->shouldReceive('hasRole')->with(['role1'])->once()->getMock();
        $this->context->hasAllowedRoles();
    }

    public function testHasRole()
    {
        $this->context->roleManager = M::mock()->shouldReceive('hasRole')->with(['role1'])->once()->getMock();
        $this->context->hasRole(['role1']);
    }

    public function testSetRoles()
    {
        $this->context->roleManager = M::mock()->shouldReceive('setRoles')->with(['role1'])->once()->getMock();
        $this->context->setRoles(['role1']);
    }

    public function testAddRoles()
    {
        $this->context->roleManager = M::mock()->shouldReceive('addRoles')->with(['role1'])->once()->getMock();
        $this->context->addRoles(['role1']);
    }

    public function testHasPreviousUrl()
    {
        $this->context->previousUrlHolder = M::mock()->shouldReceive('has')->once()->getMock();
        $this->context->hasPreviousUrl();
    }

    public function testSetPreviousUrl()
    {
        $this->context->previousUrlHolder = M::mock()->shouldReceive('set')->with('previous_url')->once()->getMock();
        $this->context->setPreviousUrl('previous_url');
    }

    public function testGetPrevioudUrl()
    {
        $this->context->previousUrlHolder = M::mock()->shouldReceive('get')->once()->getMock();
        $this->context->getPreviousUrl();
    }
}