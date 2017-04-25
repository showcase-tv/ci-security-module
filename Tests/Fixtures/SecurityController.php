<?php

namespace SCTV\Security\Tests\Fixtures;

use SCTV\Security\Annotation\Security;
use SCTV\Security\Annotation\SecurityConfig;

/**
 * @SecurityConfig(redirect_url="/signin", domain="admin")
 * @Security(roles={"normal"})
 */
class SecurityController
{
}