<?php

/**
 * Database Configuration
 */
class Database
{
    private $host = '127.0.0.1';
    private $port = 3307;
    private $db_name = 'teachme';
    private $username = 'root';
    private $password = 'mariadb';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->db_name,
                $this->port
            );

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }

        return $this->conn;
    }
}

// Create global connection
$database = new Database();
$conn = $database->getConnection();
