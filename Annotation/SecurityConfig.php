<?php

namespace SCTV\Security\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * SecurityConfig Annotation
 *
 * @Annotation
 */
class SecurityConfig
{
    public $domain = 'secure';
    public $redirect_url;
}