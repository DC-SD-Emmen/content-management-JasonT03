<?php

    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    class UserManager {

        private $conn;
        private $logFile = 'log_file.txt';

        public function __construct() {
            $db = new Database();
            $this->conn = $db->getConnection();

            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }

        public function usernameExists($username) {
            $sql = "SELECT COUNT(*) FROM Users WHERE LOWER(username) = LOWER(:username)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        }

        public function insertData($data) {
            // Link POST data to variables
            $username = $data['gebruikersnaam'];
            $password = $data['wachtwoord'];

            //Filtering
            $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

            //Regex validation
            $username_Regex = "/^[a-zA-Z0-9\s.,'?!]{1,50}$/";
            $password_Regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[a-zA-Z\d\W_]{8,}$/";

            // Matching regex to variables and adding errors
            $errors = [];

            if (!preg_match($username_Regex, $username)) {
                $errors[] = "Please enter a correct username.";
            }
            if (!preg_match($password_Regex, $password)) {
                $errors[] = "Please enter a correct password. 8characters, 1 uppercase, 1 lowercase, 1 number and 1 special character.";
            }
            if ($this->usernameExists($username)) {
                $errors[] = "Username already exists. Choose another.";
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p style='color: red;'>$error</p>";
                }
                return false;
            }

            try {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO Users (username, user_password) VALUES (:username, :passwordHash)";
        
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':passwordHash', $passwordHash);

                $stmt->execute();
                $message = date('Y-m-d H:i:s') . " - Account created successfully\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);
            
                return true;
            } 

            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Account registration failed: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
            
                return false;
            }

        }

        public function login($login_data) {
            
            $username = $login_data['gebruikersnaam'] ?? '';
            $password = $login_data['wachtwoord'] ?? '';

            $sql = "SELECT * FROM Users WHERE username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['user_password'])) {
                // Sessienaam vernieuwen voor beveiliging
                session_regenerate_id(true); 


                // Session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;
                
                $message = date('Y-m-d H:i:s') . " - Login successfully\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);

                header("Location: dashboard.php");
                exit();
            } 
            else {
                echo "<p style='color: red;'>Login failed: Invalid username or password</p>";

                $errorMessage = date('Y-m-d H:i:s') . " - Login failed: Invalid username or password\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);

                return false;
            }
        }

        public function isUserLoggedIn() {
            return isset($_SESSION['logged_in'], $_SESSION['user_id'], $_SESSION['username']) 
            && $_SESSION['logged_in'] === true;
        }

        public function logout() {
            
            session_unset();
            session_destroy();

            $message = date('Y-m-d H:i:s') . " - User logged out\n";
            file_put_contents($this->logFile, $message, FILE_APPEND);

            header('Location: index.php');
            exit();
        }

    }
?>