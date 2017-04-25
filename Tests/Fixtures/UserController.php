<?php

namespace SCTV\Security\Tests\Fixtures;

use SCTV\Security\Annotation\Security;
use SCTV\Security\Annotation\SecurityConfig;

/**
 * @SecurityConfig(redirect_url="/signin", domain="user")
 * @Security(roles={"normal"})
 */
class UserController
{
    public function securePage()
    {

    }

    /**
     * @SecurityConfig(redirect_url="/other_page")
     */
    public function otherRedirectPage()
    {

    }

    /**
     * @Security(roles={"super"})
     */
    public function otherRolePage()
    {

    }

    /**
     * @Security(roles={})
     */
    public function nonRolePage()
    {

    }

    /**
     * @Security(enabled=false)
     */
    public function publicPage()
    {

    }
}