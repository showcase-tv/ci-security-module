<?php

namespace SCTV\Security\Security;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use SCTV\Security\Annotation\SecurityConfig;
use SCTV\Security\Annotation\Security;
use SCTV\Security\Role\SessionRoleManager;
use SCTV\Security\Session\SessionCI;
use SCTV\Security\Auth\SessionAuthManager;

class AnnotationLoader
{
    public $reader;

    public function __construct()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../Annotation/Security.php');
        AnnotationRegistry::registerFile(__DIR__ . '/../Annotation/SecurityConfig.php');

        $this->reader = new AnnotationReader();
    }

    public function load(SecurityContext $context, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $_class = $class;
        $_classes = [$_class];
        while ($_class = $_class->getParentClass()) {
            $_classes[] = $_class;
        }
        $_classes = array_reverse($_classes);

        $annotations = [];
        foreach ($_classes as $_class) {
            $annotations = array_merge($annotations, $this->reader->getClassAnnotations($_class));
        }

        $annotations = array_merge($annotations, $this->reader->getMethodAnnotations($method));

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Security) {
                $context->setSecure($annotation->enabled)
                    ->setAllowedRoles($annotation->roles);
            } elseif ($annotation instanceof SecurityConfig) {
                if ($annotation->domain !== null) {
                    $context->setDomain($annotation->domain);
                }
                $context->setRedirectUrl($annotation->redirect_url);
            }
        }

        $this->fixContext($context);
    }

    public function fixContext(SecurityContext $context)
    {
        $sessionManager = new SessionCI();

        if ($context->getAuthManager() === null ) {
            $authManager = new SessionAuthManager($sessionManager);
            $context->setAuthManager($authManager);
        }

        if ($context->getPreviousUrlHolder() === null) {
            $previousUrlHolder = new PreviousUrlHolder($sessionManager);
            $context->setPreviousUrlHolder($previousUrlHolder);
        }

        if ($context->getRoleManager() === null) {
            $roleManager = new SessionRoleManager($sessionManager);
            $context->setRoleManager($roleManager);
        }

        $context->fixDomain();
    }
}
