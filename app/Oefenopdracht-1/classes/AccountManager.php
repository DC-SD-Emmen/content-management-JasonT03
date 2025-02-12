

<?php

    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    class AccountManager {

        private $conn;
        private $logFile = 'log_file.txt';

        public function __construct() {
            $db = new Database();
            $this->conn = $db->getConnection();
        }

        public function insertData($data) {

            // Link POST data to variables
            $username = $data['gebruikersnaam'];
            $passwords = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);

            //Filtering
            $username = htmlspecialchars($username);

            //Regex validation
            $username_Regex = "/^[a-zA-Z0-9\s.,'?!]{1,50}$/";
            $password_Regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/";

            // Matching regex to variables and adding errors
            $errors = [];

            if (!preg_match($username_Regex, $username)) {
                $errors[] = "Please enter a correct username.";
            }
            if (!preg_match($password_Regex, $passwords)) {
                $errors[] = "Please enter a correct password.";
            }

            // If no errors, insert data into database
            if (count($errors) == 0) {

                try {
                    $sql = "INSERT INTO users (username, passwords) VALUES (:username, :passwords)";
            
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':passwords', $passwords);

                    $stmt->execute();
                    $message = date('Y-m-d H:i:s') . " - Account created successfully";
                    file_put_contents($this->logFile, $message, FILE_APPEND);
    
                    $stmt->closeCursor();
                } 
    
                catch (PDOException $e) {
                    $errorMessage = date('Y-m-d H:i:s') . " - Account registration failed: " . $e->getMessage() . "\n";
                    file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
                }

            } 
            else {

                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
            }
        }
    }
?>