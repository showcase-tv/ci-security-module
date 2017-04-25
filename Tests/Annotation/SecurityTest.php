<?php

namespace SCTV\Security\Annotation;

use Mockery as M;

class SecurityTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testDefault()
    {
        $security = new Security();
        $this->assertTrue($security->enabled);
        $this->assertNull($security->roles);
    }

    public function tearDown()
    {
        M::close();
    }
}