<?php

namespace SCTV\Security\Annotation;

use Mockery as M;

class SecurityConfigTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testDefault()
    {
        $securityConfig = new SecurityConfig();
        $this->assertEquals('secure', $securityConfig->domain);
        $this->assertNull($securityConfig->redirect_url);
    }

    public function tearDown()
    {
        M::close();
    }
}