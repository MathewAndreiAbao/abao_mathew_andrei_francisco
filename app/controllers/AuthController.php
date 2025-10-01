<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller {
    private $auth;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('UserModel');
        $this->call->library('session');
        $this->call->library('Auth');
        $this->userModel = $this->UserModel;
        $this->auth = $this->Auth;
    }

    public function index() {
        redirect('/login');
    }

    public function register() {
        if ($this->auth->is_logged_in()) {
            redirect('/notes');
        }
        $this->call->library('form_validation');
        $this->call->view('register');
    }

    public function register_post() {
        if ($this->auth->is_logged_in()) {
            redirect('/notes');
        }

        $this->call->library('form_validation');

        $this->form_validation
            ->name('email')
                ->required('Email is required.')
                ->valid_email('Invalid email address.')
                ->is_unique('users', 'email', 'Email already registered.')
            ->name('password')
                ->required('Password is required.')
                ->min_length(6, 'Password must be at least 6 characters.')
            ->name('confirm_password')
                ->required('Confirm Password is required.')
                ->matches('password', 'Passwords do not match.')
            ->name('role')
                ->required('Role is required.')
                ->in_list('user,admin', 'Invalid role selected.');

        $email = $this->io->post('email');
        $role = $this->io->post('role');

        $data = [
            'email' => $email,
            'role' => $role
        ];

        if ($this->form_validation->run() == FALSE) {
            $this->call->view('register', $data);
            return;
        }

        $password = $this->io->post('password');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->userModel->register($email, $hashed_password, $role);

        $this->session->set_flashdata('success', 'Registration successful. Please login.');
        redirect('/login');
    }

    public function login() {
        if ($this->auth->is_logged_in()) {
            redirect('/notes');
        }
        $this->call->library('form_validation');
        $this->call->view('login');
    }

    public function login_post() {
        if ($this->auth->is_logged_in()) {
            redirect('/notes');
        }

        $this->call->library('form_validation');

        $this->form_validation
            ->name('email')
                ->required('Email is required.')
                ->valid_email('Invalid email address.')
            ->name('password')
                ->required('Password is required.');

        $email = $this->io->post('email');

        $data = [
            'email' => $email
        ];

        if ($this->form_validation->run() == FALSE) {
            $this->call->view('login', $data);
            return;
        }

        $password = $this->io->post('password');
        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $data['error'] = 'Invalid email or password.';
            $this->call->view('login', $data);
            return;
        }

        $this->session->set_userdata([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'logged_in' => true
        ]);

        redirect('/notes');
    }

    public function logout() {
        $this->auth->logout();
        redirect('/login');
    }
}