<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UserModel extends Model {
    protected $table = 'users';

    public function __construct() {
        parent::__construct();
        $this->call->database();
    }

    public function register($email, $password, $role = 'user') {
        $sql = "INSERT INTO {$this->table} (email, password, role, created_at) VALUES (?, ?, ?, ?)";
        $params = [$email, $password, $role, date('Y-m-d H:i:s')];

        try {
            error_log('register SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->insert($sql, $params);
        } catch (PDOException $e) {
            error_log('register Error: ' . $e->getMessage());
            throw new PDOException('Insert failed: ' . $e->getMessage());
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $params = [$email];

        try {
            error_log('getUserByEmail SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->fetch($sql, $params);
        } catch (PDOException $e) {
            error_log('getUserByEmail Error: ' . $e->getMessage());
            throw new PDOException('Query failed: ' . $e->getMessage());
        }
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $params = [$id];

        try {
            error_log('getUserById SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->fetch($sql, $params);
        } catch (PDOException $e) {
            error_log('getUserById Error: ' . $e->getMessage());
            throw new PDOException('Query failed: ' . $e->getMessage());
        }
    }

    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];

        try {
            error_log('emailExists SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->count($sql, $params) > 0;
        } catch (PDOException $e) {
            error_log('emailExists Error: ' . $e->getMessage());
            throw new PDOException('Query failed: ' . $e->getMessage());
        }
    }

    public function getAllUsers() {
        $sql = "SELECT * FROM {$this->table}";

        try {
            error_log('getAllUsers SQL: ' . $sql);
            return $this->db->fetchAll($sql);
        } catch (PDOException $e) {
            error_log('getAllUsers Error: ' . $e->getMessage());
            throw new PDOException('Query failed: ' . $e->getMessage());
        }
    }

    public function updateUser($id, $data) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";

        try {
            error_log('updateUser SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->update($sql, $params);
        } catch (PDOException $e) {
            error_log('updateUser Error: ' . $e->getMessage());
            throw new PDOException('Update failed: ' . $e->getMessage());
        }
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $params = [$id];

        try {
            error_log('deleteUser SQL: ' . $sql . ' | Params: ' . json_encode($params));
            return $this->db->delete($sql, $params);
        } catch (PDOException $e) {
            error_log('deleteUser Error: ' . $e->getMessage());
            throw new PDOException('Delete failed: ' . $e->getMessage());
        }
    }
}