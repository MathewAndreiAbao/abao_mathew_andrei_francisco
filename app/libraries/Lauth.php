<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');


class Lauth {

    private $session;

    public function __construct() {
      
        $this->session = null;
    }

    private function loadSession() {
        if ($this->session === null) {
            try {
                $this->session = lava_instance()->call->library('Session');
            } catch (Exception $e) {
                $this->session = null;
            }
        }
    }


    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }


    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function login($user_id, $email, $role = 'user') {
        $this->loadSession();
        if (!$this->session) return;
        $this->session->set_userdata([
            'user_id' => $user_id,
            'email' => $email,
            'role' => $role,
            'logged_in' => true
        ]);
    }


    public function logout() {
        $this->loadSession();
        if (!$this->session) return;
        $this->session->unset_userdata(['user_id', 'email', 'role', 'logged_in']);
        $this->session->sess_destroy();
    }

    
    public function isLoggedIn() {
        $this->loadSession();
        if (!$this->session) return false;
        return $this->session->userdata('logged_in') === true;
    }


    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'user_id' => $this->session->userdata('user_id'),
            'email' => $this->session->userdata('email'),
            'role' => $this->session->userdata('role')
        ];
    }


    public function getCurrentUserId() {
        $this->loadSession();
        if (!$this->session) return null;
        return $this->session->userdata('user_id');
    }


    public function getCurrentUserRole() {
        if (!$this->session) return null;
        return $this->session->userdata('role');
    }


    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            redirect('auth/login');
        }
    }


    public function requireRole($role) {
        $this->requireLogin();

        $current_role = $this->getCurrentUserRole();
        if ($current_role !== $role && $current_role !== 'admin') {
            
            show_error('Access denied. Insufficient permissions.', 403);
        }
    }


    public function hasRole($role) {
        $current_role = $this->getCurrentUserRole();
        return $current_role === $role || $current_role === 'admin';
    }
}
