<?php

namespace SCTV\Security\Auth;

use SCTV\Security\Session\SessionInterface;
use Mockery as M;

class SessionAuthManagerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var  SessionAuthManager */
    private $auth;
    /** @var  \Mockery\MockInterface */
    private $session;

    public function setUp()
    {
        $this->session = M::mock(SessionInterface::class);
        $this->auth = new SessionAuthManager($this->session);
        $this->auth->setDomain('test_domain');
    }

    public function tearDown()
    {
        M::close();
    }

    public function testGetUser()
    {
        $this->session->shouldReceive('get')
            ->with('test_domain.' . SessionAuthManager::USER, null)
            ->once()
            ->andReturn('mock_user');
        $this->assertEquals('mock_user', $this->auth->getUser());
    }

    public function testSignIn()
    {
        $this->session->shouldReceive('set')
            ->with('test_domain.' . SessionAuthManager::AUTHENTICATED, true)
            ->once();
        $this->session->shouldReceive('set')
            ->with('test_domain.' . SessionAuthManager::USER, 'user_mock')
            ->once();

        $this->auth->signIn('user_mock');
    }

    public function testSignOut()
    {
        $this->session->shouldReceive('set')
            ->with('test_domain.' . SessionAuthManager::AUTHENTICATED, false)
            ->once();
        $this->session->shouldReceive('set')
            ->with('test_domain.' . SessionAuthManager::USER, null)
            ->once();

        $this->auth->signOut();
    }

    public function testAuthenticated()
    {
        $this->session->shouldReceive('get')
            ->with('test_domain.' . SessionAuthManager::AUTHENTICATED, false)
            ->once()
            ->andReturn(true);
        $this->assertTrue($this->auth->isAuthenticated());
    }

    public function testNotAuthenticated()
    {
        $this->session->shouldReceive('get')
            ->with('test_domain.' . SessionAuthManager::AUTHENTICATED, false)
            ->once()
            ->andReturn(false);
        $this->assertFalse($this->auth->isAuthenticated());
    }
}
