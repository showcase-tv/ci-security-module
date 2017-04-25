<?php

namespace SCTV\Security\Tests\Fixtures;

use SCTV\Security\Annotation\Security;
use SCTV\Security\Annotation\SecurityConfig;

class AdminController extends SecurityController
{
    public function securePage()
    {

    }

    /**
     * @SecurityConfig(redirect_url="/super_signin")
     * @Security(roles={"super_admin"})
     */
    public function secureSuperAdminPage()
    {

    }

    /**
     * @Security(enabled=false)
     */
    public function publicPage()
    {

    }
}