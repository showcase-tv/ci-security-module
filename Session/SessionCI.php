<?php

namespace SCTV\Security\Session;

class SessionCI implements SessionInterface
{
    protected $CI;

    /**
     * SessionCI constructor.
     */
    public function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->library('session');
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default)
    {
        if (!$this->CI->session->has_userData($key)) {
            return $default;
        }
        return $this->CI->session->userdata($key);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->CI->session->set_userdata($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function remove($key)
    {
        $this->CI->session->unset_userdata($key);
    }
}