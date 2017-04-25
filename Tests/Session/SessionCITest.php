<?php

namespace SCTV\Security\Session;

use SCTV\Security\Session\SessionCI;
use Mockery as M;

function get_instance()
{
    return SessionCITest::$function->get_instance();
}

class SessionCITest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var  \Mockery\MockInterface */
    private $CI;
    /** @var  \Mockery\MockInterface */
    private $CISession;
    /** @var  SessionCI */
    private $session;

    public static $function;

    public function setUp()
    {
        $this->CISession = M::mock();

        $this->CI = M::mock();
        $this->CI->session = $this->CISession;
        $this->CI->load = M::mock()->shouldReceive('library')->getMock();

        self::$function = M::mock();
        self::$function->shouldReceive('get_instance')->once()->andReturn($this->CI);

        $this->session = new SessionCI();
    }

    public function tearDown()
    {
        M::close();
    }

    public function testGetReturnDefault()
    {
        $this->CISession->shouldReceive('has_userData')->twice()->andReturn(false);
        $this->assertFalse($this->session->get('key', false));
        $this->assertNull($this->session->get('key', null));
    }

    public function testGet()
    {
        $this->CISession->shouldReceive('has_userData')->once()->andReturn(true);
        $this->CISession->shouldReceive('userdata')->with('key')->once();
        $this->session->get('key', false);
    }

    public function testSet()
    {
        $this->CISession->shouldReceive('set_userdata')->with('key', 'dummy_data')->once();
        $this->session->set('key', 'dummy_data');
    }

    public function testRemove()
    {
        $this->CISession->shouldReceive('unset_userdata')->with('key')->once();
        $this->session->remove('key');
    }
}