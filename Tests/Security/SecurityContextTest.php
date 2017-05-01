<?php

namespace SCTV\Security\Security;

use Mockery as M;
use SCTV\Security\Auth\AuthManagerInterface;
use SCTV\Security\Role\RoleManagerInterface;

class SecurityContextTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var  SecurityContext */
    private $context;

    /** @var  \Mockery\MockInterface */
    private $roleManager;
    /** @var  \Mockery\MockInterface */
    private $previousUrlHolder;
    /** @var  \Mockery\MockInterface */
    private $authManager;

    public function setUp()
    {
        $this->roleManager = M::mock(RoleManagerInterface::class);
        $this->previousUrlHolder = M::mock(PreviousUrlHolder::class);
        $this->authManager = M::mock(AuthManagerInterface::class);

        $this->context = SecurityContext::getInstance();
        $this->context->setRoleManager($this->roleManager);
        $this->context->setPreviousUrlHolder($this->previousUrlHolder);
        $this->context->setAuthManager($this->authManager);
    }

    public function tearDown()
    {
        M::close();
    }

    public function testCallIsAuthenticated()
    {
        $this->authManager->shouldReceive('isAuthenticated')->once()->andReturn(true);
        $this->assertTrue(true, $this->context->isAuthenticated());
    }

    public function testDefault()
    {
        $this->assertEquals('secure', $this->context->getDomain());
    }

    public function testSetSecure()
    {
        $this->assertFalse($this->context->isSecure());
        $this->context->setSecure(true);
        $this->assertTrue($this->context->isSecure());
    }

    public function testDomain()
    {
        $this->context->setDomain('test_domain');
        $this->assertEquals('test_domain', $this->context->getDomain());
    }

    public function testRedirectUrl()
    {
        $this->context->setRedirectUrl('redirect_url');
        $this->assertEquals('redirect_url', $this->context->getRedirectUrl());
    }

    public function testAllowedRoles()
    {
        $this->context->setAllowedRoles('role1');
        $this->assertEquals(['role1'], $this->context->getAllowedRoles());
    }

    public function testAuthManager()
    {
        $mock = M::mock('SCTV\Security\Auth\AuthManagerInterface');
        $this->context->setAuthManager($mock);
        $this->assertEquals($mock, $this->context->getAuthManager());
    }

    public function testFixDomain()
    {
        $this->context->setDomain('test_domain');
        $this->authManager->shouldReceive('setDomain')->with('test_domain')->getMock();
        $this->roleManager->shouldReceive('setDomain')->with('test_domain')->once()->getMock();
        $this->previousUrlHolder->shouldReceive('setUp')->with('test_domain')->once()->getMock();
        $this->context->fixDomain();
    }

    public function testSignIn()
    {
        $this->authManager->shouldReceive('signIn')->with(['user_data'])->once()->getMock();
        $this->context->signIn(['user_data']);
    }

    public function testSignOut()
    {
        $this->authManager->shouldReceive('signOut')->once()->getMock();
        $this->roleManager->shouldReceive('clearRoles')->once()->getMock();
        $this->context->signOut();
    }

    public function testGetUser()
    {
        $this->authManager->shouldReceive('getUser')->once()->getMock();
        $this->context->getUser();
    }

    public function testPreviousUrlHolder()
    {
        $this->assertEquals($this->previousUrlHolder, $this->context->getPreviousUrlHolder());
    }

    public function testRoleManager()
    {
        $this->assertEquals($this->roleManager, $this->context->getRoleManager());
    }

    public function testHasAllowedRoles()
    {
        $this->context->setAllowedRoles(['role1']);
        $this->roleManager->shouldReceive('hasRole')->with(['role1'])->once()->getMock();
        $this->context->hasAllowedRoles();
    }

    public function testHasRole()
    {
        $this->roleManager->shouldReceive('hasRole')->with(['role1'])->once()->getMock();
        $this->context->hasRole(['role1']);
    }

    public function testSetRoles()
    {
        $this->roleManager->shouldReceive('setRoles')->with(['role1'])->once()->getMock();
        $this->context->setRoles(['role1']);
    }

    public function testAddRoles()
    {
        $this->roleManager->shouldReceive('addRoles')->with(['role1'])->once()->getMock();
        $this->context->addRoles(['role1']);
    }

    public function testHasPreviousUrl()
    {
        $this->previousUrlHolder->shouldReceive('has')->once()->getMock();
        $this->context->hasPreviousUrl();
    }

    public function testSetPreviousUrl()
    {
        $this->previousUrlHolder->shouldReceive('set')->with('previous_url')->once()->getMock();
        $this->context->setPreviousUrl('previous_url');
    }

    public function testGetPrevioudUrl()
    {
        $this->previousUrlHolder->shouldReceive('get')->once()->getMock();
        $this->context->getPreviousUrl();
    }
}