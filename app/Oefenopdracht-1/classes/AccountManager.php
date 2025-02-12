

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
            $password = $data['wachtwoord'];

            //Filtering
            $username = htmlspecialchars($username);

            //Regex validation
            $username_Regex = "/^[a-zA-Z0-9\s.,'?!]{1,50}$/";
            $password_Regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[a-zA-Z\d\W_]{8,}$/";

            // Matching regex to variables and adding errors
            $errors = [];

            if (!preg_match($username_Regex, $username)) {
                $errors[] = "Please enter a correct username.";
            }
            if (!preg_match($password_Regex, $password)) {
                $errors[] = "Please enter a correct password.";
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
                return false;
            }

            try {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, user_password) VALUES (:username, :passwordHash)";
        
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':passwordHash', $passwordHash);

                $stmt->execute();
                $message = date('Y-m-d H:i:s') . " - Account created successfully";
                file_put_contents($this->logFile, $message, FILE_APPEND);
            } 

            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Account registration failed: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
            }

        }

        public function login($login_data) {
            session_start();

            $username = $login_data['gebruikersnaam'];
            $password = $login_data['wachtwoord'];

            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['passwords'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                $message = date('Y-m-d H:i:s') . " - Login successfully";
                file_put_contents($this->logFile, $message, FILE_APPEND);

                return true;
            } 
            else {
                $errorMessage = date('Y-m-d H:i:s') . " - Login failed: Invalid username or password\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);

                return false;
            }
        }

    }
?>