<?php

namespace SCTV\Security\Role;

use SCTV\Security\Session\SessionInterface;
use Mockery as M;

class SessionRoleManagerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var  SessionRoleManager */
    private $role;
    /** @var  \Mockery\MockInterface */
    private $session;

    public function setUp()
    {
        $this->session = M::mock(SessionInterface::class);
        $this->role = new SessionRoleManager($this->session);
        $this->role->setDomain('test_domain');
    }

    public function tearDown()
    {
        M::close();
    }

    public function testAddRoles()
    {
        $this->session->shouldReceive('get')
            ->with('test_domain.' . SessionRoleManager::ROLE, [])
            ->once()
            ->andReturn([]);
        $this->session->shouldReceive('set')
            ->with('test_domain.' . SessionRoleManager::ROLE, ['TEST_ROLE'])
            ->once();

        $this->role->addRoles('TEST_ROLE');
    }

    public function testSetRoles()
    {
        $this->session->shouldReceive('set')
            ->with('test_domain.' . SessionRoleManager::ROLE, ['TEST_ROLE'])
            ->once();
        $this->role->setRoles('TEST_ROLE');
    }

    public function testHasRoleReturnTruePattern1()
    {
        $this->session->shouldReceive('get')->never();
        $this->assertTrue($this->role->hasRole([]));
    }

    public function testHasRoleReturnTruePattern2()
    {
        $this->session->shouldReceive('get')->once()->andReturn(['HOGE']);
        $this->assertTrue($this->role->hasRole(['HOGE']));
    }

    public function testHasRoleReturnTruePattern3()
    {
        $this->session->shouldReceive('get')->once()->andReturn(['HOGE', 'FUGA']);
        $this->assertTrue($this->role->hasRole(['HOGE']));
    }

    public function testHasRoleReturnTruePattern4()
    {
        $this->session->shouldReceive('get')->once()->andReturn(['MOGE']);
        $this->assertTrue($this->role->hasRole(['HOGE', 'MOGE']));
    }

    public function testHasRoleReturnFalsePattern1()
    {
        $this->session->shouldReceive('get')->once()->andReturn([]);
        $this->assertFalse($this->role->hasRole(['HOGE']));
    }

    public function testHasRoleReturnFalsePattern2()
    {
        $this->session->shouldReceive('get')->once()->andReturn(['FUGA']);
        $this->assertFalse($this->role->hasRole(['HOGE']));
    }

    public function testHasRoleReturnFalsePattern3()
    {
        $this->session->shouldReceive('get')->once()->andReturn(['FUGA']);
        $this->assertFalse($this->role->hasRole(['HOGE', 'MOGE']));
    }

    public function testClearRole()
    {
        $this->session->shouldReceive('set')
            ->with('test_domain.' . SessionRoleManager::ROLE, [])
            ->once();
        $this->role->clearRoles();
    }
}