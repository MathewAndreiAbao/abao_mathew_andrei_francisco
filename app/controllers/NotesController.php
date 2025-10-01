<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class NotesController extends Controller {
    private $auth;
    private $noteModel;

    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('NoteModel');
        $this->call->library('session');
        $this->call->library('Auth');
        $this->noteModel = $this->NoteModel;
        $this->auth = $this->Auth;

        if (!$this->auth->is_logged_in()) {
            redirect('/login');
        }
    }

    public function index($page = null) {
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $search = trim($_GET['search'] ?? '');

        // Fetch paginated results for all notes, regardless of role
        $result = $this->noteModel->findAll($page, 6, $search);

        $this->call->view('index', [
            'notes' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'role' => $role
        ]);
    }

    public function create() {
        $role = $this->session->userdata('role');
        if ($role !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Only admins can create notes.');
            redirect('/notes');
        }

        $this->call->library('form_validation');
        $this->call->view('create');
    }

    public function create_post() {
        $role = $this->session->userdata('role');
        if ($role !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Only admins can create notes.');
            redirect('/notes');
        }

        $this->call->library('form_validation');

        $this->form_validation
            ->name('title')
                ->required('Title is required.')
                ->min_length(1, 'Title must be at least 1 character.')
                ->max_length(255, 'Title cannot exceed 255 characters.')
            ->name('content')
                ->required('Content is required.');

        if ($this->form_validation->run() == FALSE) {
            $data = [
                'title' => $this->io->post('title'),
                'content' => $this->io->post('content')
            ];
            $this->call->view('create', $data);
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $title = $this->io->post('title');
        $content = $this->io->post('content');

        if ($this->noteModel->createNote($user_id, $title, $content)) {
            $this->session->set_flashdata('success', 'Note created successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to create note.');
        }
        redirect('/notes');
    }

    public function edit($id) {
        $role = $this->session->userdata('role');
        if ($role !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Only admins can edit notes.');
            redirect('/notes');
        }

        $this->call->library('form_validation');
        $user_id = $this->session->userdata('user_id');

        $note = $this->noteModel->getNoteById($id);

        if (!$note) {
            show_error('Note not found or access denied.', 404);
        }

        $this->call->view('edit', ['note' => $note]);
    }

    public function edit_post($id) {
        $role = $this->session->userdata('role');
        if ($role !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Only admins can edit notes.');
            redirect('/notes');
        }

        $this->call->library('form_validation');

        $this->form_validation
            ->name('title')
                ->required('Title is required.')
                ->min_length(1, 'Title must be at least 1 character.')
                ->max_length(255, 'Title cannot exceed 255 characters.')
            ->name('content')
                ->required('Content is required.');

        $note = $this->noteModel->getNoteById($id);

        if (!$note) {
            show_error('Note not found or access denied.', 404);
        }

        if ($this->form_validation->run() == FALSE) {
            $note['title'] = $this->io->post('title');
            $note['content'] = $this->io->post('content');
            $this->call->view('edit', ['note' => $note]);
            return;
        }

        $title = $this->io->post('title');
        $content = $this->io->post('content');

        if ($this->noteModel->updateNote($id, $title, $content)) {
            $this->session->set_flashdata('success', 'Note updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update note.');
        }
        redirect('/notes');
    }

    public function delete($id) {
        $role = $this->session->userdata('role');
        if ($role !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Only admins can delete notes.');
            redirect('/notes');
        }

        if ($this->noteModel->deleteNote($id)) {
            $this->session->set_flashdata('success', 'Note deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete note.');
        }
        redirect('/notes');
    }
}