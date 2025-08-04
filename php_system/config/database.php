<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'data_management_system';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    public function createDatabase() {
        try {
            // Connect without database name first
            $conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $conn->exec("set names utf8");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
            
            // Now connect to the database
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->createTables();
            
            return true;
        } catch(PDOException $exception) {
            echo "Database creation error: " . $exception->getMessage();
            return false;
        }
    }

    private function createTables() {
        // Create OFAC table
        $ofac_table = "CREATE TABLE IF NOT EXISTS ofac_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source_media VARCHAR(255),
            source_media_date DATE,
            resolution TEXT,
            individual_corporation_involved VARCHAR(255),
            first_name VARCHAR(100),
            middle_name VARCHAR(100),
            last_name VARCHAR(100),
            name_ext VARCHAR(50),
            corporation_name_fullname VARCHAR(255),
            alternate_name_alias_1 VARCHAR(255),
            alternate_name_alias_2 VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        // Create IIBS table
        $iibs_table = "CREATE TABLE IF NOT EXISTS iibs_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source_media VARCHAR(255),
            source_media_date DATE,
            resolution TEXT,
            individual_corporation_involved VARCHAR(255),
            first_name VARCHAR(100),
            middle_name VARCHAR(100),
            last_name VARCHAR(100),
            name_ext VARCHAR(50),
            corporation_name_fullname VARCHAR(255),
            alternate_name_alias_1 VARCHAR(255),
            alternate_name_alias_2 VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $this->conn->exec($ofac_table);
        $this->conn->exec($iibs_table);
    }
}
?>
