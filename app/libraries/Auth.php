<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth {
    protected $_lava;
    protected $session;

    public function __construct($session = null) {
        $this->_lava = lava_instance();
        if ($session !== null) {
            $this->session = $session;
        } else if (isset($this->_lava->session)) {
            $this->session = $this->_lava->session;
        } else {
            $this->_lava->call->library('session');
            $this->session = $this->_lava->session;
        }
    }

    public function is_logged_in() {
        return (bool) $this->session->userdata('logged_in');
    }

    public function has_role($role) {
        return $this->session->userdata('role') === $role;
    }

    public function logout() {
        $this->session->unset_userdata(['user_id', 'email', 'role', 'logged_in']);
    }
}