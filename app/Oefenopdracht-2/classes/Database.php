<?php

    class Database {
            
        // Connection details
        private $servername = "mysql";
        private $username = "root";
        private $password = "root";
        private $dbname = "UserLogin";
        private $conn;
        private $logFile = 'log_file.txt';
        
        // Making connection with mysql
        public function __construct() {
            try {
                $this->conn = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $message = date('Y-m-d H:i:s') . " - Connected successfully\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);
                
            } catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Connection failed: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
            }
        }
        
        public function getConnection() {
            return $this->conn;
        }
    }
?>