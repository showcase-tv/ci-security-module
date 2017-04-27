<?php

namespace SCTV\Security\Hook;

use Mockery as M;
use SCTV\Security\Tests\Fixtures\UserController;

function get_instance()
{
    return AuthListenerTest::$function->get_instance();
}

function uri_string()
{
    return 'dummy_url';
}

function header($param)
{
    AuthListenerTest::$header->call($param);
}

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AuthListenerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var  \Mockery\MockInterface */
    private $CI;
    /** @var  \Mockery\MockInterface */
    private $context;
    /** @var  \Mockery\MockInterface */
    private $loader;

    /** @var  AuthListener */
    private $authListener;

    public static $function;
    public static $header;

    public function setUp()
    {
        $this->CI = M::mock();
        $this->CI->load = M::mock()->shouldReceive('helper')->getMock();
        $this->CI->router = M::mock();

        $this->context = M::mock('SecurityContext');

        M::mock('alias:SCTV\Security\Security\SecurityContext')
            ->shouldReceive('getInstance')
            ->once()
            ->andReturn($this->context);

        $this->CI->router->class = UserController::class;
        $this->CI->router->method = 'securePage';

        $this->loader = M::mock('overload:SCTV\Security\Security\AnnotationLoader');

        self::$function = M::mock();
        self::$function->shouldReceive('get_instance')->andReturn($this->CI);
        self::$header = M::mock();

        $this->authListener = M::mock(AuthListener::class)->makePartial();
        $this->authListener->__construct();
    }

    public function tearDown()
    {
        M::close();
    }

    public function testLoad()
    {
        $this->loader->shouldReceive('load')->with($this->context, 'ReflectionClass', 'ReflectionMethod')->once();

        $this->authListener->load();
    }

    public function testNotSecure()
    {
        $this->context->shouldReceive('isSecure')->once()->andReturn(false);
        $this->context->shouldReceive('isAuthenticated')->never();
        $this->context->shouldReceive('hasAllowedRoles')->never();

        $this->authListener->authenticate();
    }

    public function testNotAuthenticated()
    {
        $this->authListener->shouldReceive('redirect')->once();
        $this->context->shouldReceive('isSecure')->once()->andReturn(true);
        $this->context->shouldReceive('isAuthenticated')->once()->andReturn(false);
        $this->context->shouldReceive('setPreviousUrl')->with('/dummy_url')->once();

        $this->authListener->authenticate();
    }

    public function testNotAllowedRoles()
    {
        $this->authListener->shouldReceive('redirect')->once();
        $this->context->shouldReceive('isSecure')->once()->andReturn(true);
        $this->context->shouldReceive('isAuthenticated')->once()->andReturn(true);
        $this->context->shouldReceive('hasAllowedRoles')->once()->andReturn(false);
        $this->context->shouldReceive('setPreviousUrl')->never();

        $this->authListener->authenticate();
    }

    public function testHasAllowedRoles()
    {
        $this->context->shouldReceive('isSecure')->once()->andReturn(true);
        $this->context->shouldReceive('isAuthenticated')->once()->andReturn(true);
        $this->context->shouldReceive('hasAllowedRoles')->once()->andReturn(true);
        $this->context->shouldReceive('setPreviousUrl')->never();

        $this->authListener->authenticate();
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage redirect_urlを設定してください。
     */
    public function testNullRedirectUrl()
    {
        $this->context->shouldReceive('getRedirectUrl')->once()->andReturn(null);
        $this->authListener->redirect();
    }

    public function testRedirectUrl()
    {
        $this->context->shouldReceive('getRedirectUrl')->twice()->andReturn('redirect_url');
        self::$header->shouldReceive('call')->with('location: redirect_url')->once();
        $this->authListener->redirect();
    }

    public function testPostControllerConstructor()
    {
        $this->authListener->shouldReceive('load')->once();
        $this->authListener->shouldReceive('authenticate')->once();

        $this->authListener->postControllerConstructor();
    }

}
