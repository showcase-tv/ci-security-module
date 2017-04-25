<?php

namespace SCTV\Security\Hook;

use SCTV\Security\Security\Authenticator;
use SCTV\Security\Security\SecurityContext;
use SCTV\Security\Security\AnnotationLoader;

/**
 * Class AuthListener
 */
class AuthListener
{
    protected $CI;
    protected $context;

    public function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->helper('url');
        $this->context = SecurityContext::getInstance();
    }

    public function load()
    {
        $class = new \ReflectionClass($this->CI->router->class);
        $method = $class->getMethod($this->CI->router->method);

        $loader = new AnnotationLoader();
        $loader->load($this->context, $class, $method);
    }

    public function postControllerConstructor()
    {
        $this->load();
        $this->authenticate();
    }

    public function authenticate()
    {
        if (!$this->context->isSecure()) {
            return;
        }

        if (!$this->context->isAuthenticated()) {
            $this->context->setPreviousUrl('/' . uri_string());
            $this->redirect();
            return;
        }

        if (!$this->context->hasAllowedRoles()) {
            $this->redirect();
            return;
        }
    }

    public function redirect()
    {
        if ($this->context->getRedirectUrl() === null) {
            throw new \Exception('redirect_urlを設定してください。');
        }

        header('location: ' . $this->context->getRedirectUrl());
    }
}