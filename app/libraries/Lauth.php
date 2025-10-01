<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * LavaLust Authentication Library
 * Handles user authentication, sessions, and role-based access
 */
class Lauth {

    private $session;

    public function __construct() {
        // Session will be loaded lazily
        $this->session = null;
    }

    /**
     * Load session library lazily
     */
    private function loadSession() {
        if ($this->session === null) {
            try {
                $this->session = lava_instance()->call->library('Session');
            } catch (Exception $e) {
                $this->session = null;
            }
        }
    }

    /**
     * Hash password using bcrypt
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify password against hash
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Log in user by setting session data
     */
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

    /**
     * Log out user by destroying session
     */
    public function logout() {
        $this->loadSession();
        if (!$this->session) return;
        $this->session->unset_userdata(['user_id', 'email', 'role', 'logged_in']);
        $this->session->sess_destroy();
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        $this->loadSession();
        if (!$this->session) return false;
        return $this->session->userdata('logged_in') === true;
    }

    /**
     * Get current user data
     */
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

    /**
     * Get current user ID
     */
    public function getCurrentUserId() {
        $this->loadSession();
        if (!$this->session) return null;
        return $this->session->userdata('user_id');
    }

    /**
     * Get current user role
     */
    public function getCurrentUserRole() {
        if (!$this->session) return null;
        return $this->session->userdata('role');
    }

    /**
     * Require user to be logged in, redirect to login if not
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            redirect('auth/login');
        }
    }

    /**
     * Require specific role, redirect if not authorized
     */
    public function requireRole($role) {
        $this->requireLogin();

        $current_role = $this->getCurrentUserRole();
        if ($current_role !== $role && $current_role !== 'admin') {
            // Only admin can access admin routes, users can't access admin
            show_error('Access denied. Insufficient permissions.', 403);
        }
    }

    /**
     * Check if current user has specific role
     */
    public function hasRole($role) {
        $current_role = $this->getCurrentUserRole();
        return $current_role === $role || $current_role === 'admin';
    }
}
