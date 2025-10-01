<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Database Class
 * Lightweight PDO wrapper for database operations
 */
class Database {
    private static $instance = null;
    private $db = null;
    private $lastIDInserted = 0;
    private $rowCount = 0;

    public function __construct($dbname = null) {
        $config = $dbname && isset(database_config()[$dbname]) ? database_config()[$dbname] : database_config()['main'];
        $driver = strtolower($config['driver']);
        $host = $config['hostname'];
        $port = $config['port'];
        $dbname_value = $config['database'];
        $username = $config['username'];
        $password = $config['password'];
        $charset = $config['charset'];
        $path = $config['path'] ?? null;

        switch ($driver) {
            case 'mysql':
                $dsn = "mysql:host=$host;dbname=$dbname_value;charset=$charset;port=$port";
                break;
            case 'pgsql':
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname_value;user=$username;password=$password";
                break;
            case 'sqlite':
                if (empty($path)) {
                    throw new PDOException('SQLite requires a valid file path.');
                }
                $dsn = "sqlite:$path";
                break;
            case 'sqlsrv':
                $dsn = "sqlsrv:Server=$host,$port;Database=$dbname_value";
                break;
            default:
                throw new PDOException("Unsupported database driver: $driver");
        }

        try {
            $this->db = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (Exception $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public static function instance($dbname = null) {
        if (!self::$instance) {
            self::$instance = new Database($dbname);
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $this->rowCount = $stmt->rowCount();
            return $stmt;
        } catch (Exception $e) {
            throw new PDOException($e->getMessage() . ' Query: ' . $sql . ' Params: ' . json_encode($params));
        }
    }

    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        $this->lastIDInserted = $this->db->lastInsertId();
        return $this->lastIDInserted;
    }

    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function delete($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function count($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }

    public function lastId() {
        return $this->lastIDInserted;
    }

    public function rowCount() {
        return $this->rowCount;
    }

    public function __destruct() {
        $this->db = null;
    }
}