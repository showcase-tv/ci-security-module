<?php
namespace SCTV\Security\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Security Annotation
 *
 * @Annotation
 */
class Security
{
    public $enabled = true;
    public $roles;
}