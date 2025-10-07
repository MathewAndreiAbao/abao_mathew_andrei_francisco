<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class NoteModel extends Model {
    protected $table = 'notes';

    public function __construct() {
        parent::__construct();
        $this->call->database();
    }

    public function getNotesByUser($user_id, $search = '', $limit_clause = '') {
        $search = trim($search);
        $sql = "SELECT notes.*, users.email as user_email FROM {$this->table} LEFT JOIN users ON notes.user_id = users.id WHERE notes.user_id = ?";
        $params = [$user_id];

        if (!empty($search)) {
            $sql .= " AND (notes.title LIKE ? OR notes.content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY notes.created_at DESC";

        if (!empty($limit_clause)) {
            $sql .= " $limit_clause";
        }

        try {
            error_log('getNotesByUser SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->fetchAll($sql, $params);
        } catch (PDOException $e) {
            error_log('getNotesByUser Error: ' . $e->getMessage());
            throw new PDOException('Query failed: ' . $e->getMessage());
        }
    }

    public function getTotalNotes($user_id, $search = '') {
        $search = trim($search);
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ?";
        $params = [$user_id];

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        try {
            error_log('getTotalNotes SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->count($sql, $params);
        } catch (PDOException $e) {
            error_log('getTotalNotes Error: ' . $e->getMessage());
            throw new PDOException('Count query failed: ' . $e->getMessage());
        }
    }

    public function getNoteById($id, $user_id = null) {
        $sql = "SELECT notes.*, users.email as user_email FROM {$this->table} LEFT JOIN users ON notes.user_id = users.id WHERE notes.id = ?";
        $params = [$id];

        if ($user_id !== null) {
            $sql .= " AND notes.user_id = ?";
            $params[] = $user_id;
        }

        try {
            error_log('getNoteById SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->fetch($sql, $params);
        } catch (PDOException $e) {
            error_log('getNoteById Error: ' . $e->getMessage());
            throw new PDOException('Query failed: ' . $e->getMessage());
        }
    }

    public function createNote($user_id, $title, $content) {
        $sql = "INSERT INTO {$this->table} (user_id, title, content, created_at) VALUES (?, ?, ?, ?)";
        $now = date('Y-m-d H:i:s');
        $params = [$user_id, $title, $content, $now];

        try {
            error_log('createNote SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->insert($sql, $params);
        } catch (PDOException $e) {
            error_log('createNote Error: ' . $e->getMessage());
            throw new PDOException('Insert failed: ' . $e->getMessage());
        }
    }

    public function updateNote($id, $title, $content, $user_id = null) {
        $sql = "UPDATE {$this->table} SET title = ?, content = ? WHERE id = ?";
        $params = [$title, $content, $id];

        if ($user_id !== null) {
            $sql .= " AND user_id = ?";
            $params[] = $user_id;
        }

        try {
            error_log('updateNote SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->update($sql, $params);
        } catch (PDOException $e) {
            error_log('updateNote Error: ' . $e->getMessage());
            throw new PDOException('Update failed: ' . $e->getMessage());
        }
    }

    public function deleteNote($id, $user_id = null) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $params = [$id];

        if ($user_id !== null) {
            $sql .= " AND user_id = ?";
            $params[] = $user_id;
        }

        try {
            error_log('deleteNote SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->delete($sql, $params);
        } catch (PDOException $e) {
            error_log('deleteNote Error: ' . $e->getMessage());
            throw new PDOException('Delete failed: ' . $e->getMessage());
        }
    }

    public function getAllNotes($search = '', $limit_clause = '') {
        $search = trim($search);
        $sql = "SELECT notes.*, users.email as user_email FROM {$this->table} LEFT JOIN users ON notes.user_id = users.id";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE notes.title LIKE ? OR notes.content LIKE ?";
            $params = ["%$search%", "%$search%"];
        }

        $sql .= " ORDER BY notes.created_at DESC";

        if (!empty($limit_clause)) {
            $sql .= " $limit_clause";
        }

        try {
            error_log('getAllNotes SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->fetchAll($sql, $params);
        } catch (PDOException $e) {
            error_log('getAllNotes Error: ' . $e->getMessage());
            throw new PDOException('Query failed: ' . $e->getMessage());
        }
    }

    public function getTotalAllNotes($search = '') {
        $search = trim($search);
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE title LIKE ? OR content LIKE ?";
            $params = ["%$search%", "%$search%"];
        }

        try {
            error_log('getTotalAllNotes SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->count($sql, $params);
        } catch (PDOException $e) {
            error_log('getTotalAllNotes Error: ' . $e->getMessage());
            throw new PDOException('Count query failed: ' . $e->getMessage());
        }
    }

       public function findAllByUser($user_id, $page = 1, $per_page = 6, $search = '') {
        $page = max(1, (int)$page);
        $per_page = max(1, (int)$per_page);
        $offset = ($page - 1) * $per_page;

        $where = 'WHERE user_id = ?';
        $params = [$user_id];

        if (!empty($search)) {
            $like = '%' . $search . '%';
            $where .= ' AND (title LIKE ? OR content LIKE ?)';
            $params[] = $like;
            $params[] = $like;
        }

      
        $total = $this->getTotalNotes($user_id, $search);

      
        $limit_clause = "LIMIT " . (int)$offset . "," . (int)$per_page;
        $data = $this->getNotesByUser($user_id, $search, $limit_clause);

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $per_page,
            'current_page' => $page,
            'last_page' => $total ? ceil($total / $per_page) : 1
        ];
    }

 
    public function findAll($page = 1, $per_page = 6, $search = '') {
        $page = max(1, (int)$page);
        $per_page = max(1, (int)$per_page);
        $offset = ($page - 1) * $per_page;

        $params = [];

        if (!empty($search)) {
            $like = '%' . $search . '%';
            $params = [$like, $like];
        }

        // Get total using COUNT (reuse existing logic)
        $total = $this->getTotalAllNotes($search);

        // Fetch page results
        $limit_clause = "LIMIT " . (int)$offset . "," . (int)$per_page;
        $data = $this->getAllNotes($search, $limit_clause);

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $per_page,
            'current_page' => $page,
            'last_page' => $total ? ceil($total / $per_page) : 1
        ];
    }
}