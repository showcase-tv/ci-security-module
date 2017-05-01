<?php

namespace SCTV\Security\Security;

use SCTV\Security\Session\SessionInterface;
use Mockery as M;

class PreviousUrlHolderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var  PreviousUrlHolder */
    private $urlHolder;
    /** @var  \Mockery\MockInterface */
    private $session;

    public function setUp()
    {
        $this->session = M::mock(SessionInterface::class);
        $this->urlHolder = new PreviousUrlHolder($this->session);

        $this->session->shouldReceive('get')->with('test_domain.' . PreviousUrlHolder::PREVIOUSURL, null)->once();
        $this->urlHolder->setUp('test_domain');
    }

    public function tearDown()
    {
        M::close();
    }

    public function testSetUp()
    {
        $this->session->shouldReceive('get')->with('test_domain.' . PreviousUrlHolder::PREVIOUSURL, null)->once();
        $this->urlHolder->setUp('test_domain');
    }

    public function testHasUrlReturnFalse()
    {
        $this->assertFalse($this->urlHolder->has());
    }

    public function testHasUrlReturnTrue()
    {
        $this->session->shouldReceive('set')->once();
        $this->urlHolder->set('redirect_url');
        $this->assertTrue($this->urlHolder->has());
    }

    public function testUrl()
    {
        $this->session->shouldReceive('set')->with('test_domain.' . PreviousUrlHolder::PREVIOUSURL,
            'redirect_url')->once();
        $this->urlHolder->set('redirect_url');
        $this->session->shouldReceive('remove')->with('test_domain.' . PreviousUrlHolder::PREVIOUSURL)->once();
        $this->assertEquals('redirect_url', $this->urlHolder->get());
    }
}