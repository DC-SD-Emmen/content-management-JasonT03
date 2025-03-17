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

        public function emailExists($email) {
            $sql = "SELECT COUNT(*) FROM Users WHERE LOWER(email) = LOWER(:email)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        }

        public function fetchUserById($user_id) {
            try {
                $sql = "SELECT * FROM Users WHERE id = :user_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                return $user ?: false;
            } 
            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Failed to fetch user with ID: $user_id - Error: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);

                return false;	
            }
        }

        private function validateUserData($data, $user_id, $user) {
            // Link POST data to variables
            $username = $data['gebruikersnaam'];
            $email = $data['email'];
            $password = $data['wachtwoord'];
        
            // Filtering input
            $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        
            // Regex validation patterns
            $username_Regex = "/^[a-zA-Z0-9.,'?!]{1,50}$/";
            $email_Regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,50}$/";
            $password_Regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[a-zA-Z\d\W_]{8,}$/";
        
            // Fetch user data           
            if ($user_id !== null && $user === false) {
                echo "<p style='color: red;'>User not found. Cannot update.</p>";
                return false;
            }

            // Error handling
            $errors = [];
        
            if ($user_id === null) {
                if (empty($username)) {
                    $errors[] = "Please enter a username.";
                }
                if (empty($email)) {
                    $errors[] = "Please enter an email.";
                }
                if (empty($password)) {
                    $errors[] = "Please enter a password.";
                }
            }
            elseif (
                (empty($username) || $username === $user['username']) && 
                (empty($email) || $email === $user['email']) && 
                (empty($password) || password_verify($password, $user['user_password']))
            ) {
                echo "<p style='color: red;'>Please fill in / change at least one field.</p>";
                return false;
            }

            if (!empty($username) && !preg_match($username_Regex, $username)) {
                $errors[] = "Please enter a correct username.";
            }
        
            if (!empty($email) && !preg_match($email_Regex, $email)) {
                $errors[] = "Please enter a correct email.";
            }
        
            if (!empty($password) && !preg_match($password_Regex, $password)) {
                $errors[] = "Please enter a correct password. Must have at least 8 characters, 1 uppercase, 1 lowercase, 1 number, and 1 special character.";
            }
        
            // Check if filled in, exists, and if user is either empty or not the same as the user in the database.
            if (!empty($username) && $this->usernameExists($username) && (!$user || $username !== $user['username'])) {
                $errors[] = "Username already exists. Choose another.";
            }
        
            if (!empty($email) && $this->emailExists($email) && (!$user || $email !== $user['email'])) {
                $errors[] = "Email already exists. Choose another.";
            }
        
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p style='color: red;'>$error</p>";
                }
                return false;
            }

            // Return validated data
            return [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'user' => $user
            ];
        }

        public function insertData($data) {

            // Validate user data
            $validatedData = $this->validateUserData($data, null, null);
            
            if (!$validatedData) {
                return false;
            }

            $username = $validatedData['username'];
            $email = $validatedData['email'];
            $password = $validatedData['password'];

            try {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO Users (username, email, user_password) VALUES (:username, :email, :passwordHash)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':passwordHash', $passwordHash);
                $stmt->execute();

                $message = date('Y-m-d H:i:s') . " - Account created successfully\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);
                
                header("Location: login.php");
                return true;
            } 

            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Account registration failed: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
            
                return false;
            }

        }

        public function changeData($data, $user_id) {
            $currentPassword = $data['huidig-wachtwoord'] ?? '';

            $user = $this->fetchUserById($user_id);
            if ($user_id !== null && $user === false) {
                echo "<p style='color: red;'>User not found. Cannot update.</p>";
                return false;
            }

            if ($user && password_verify($currentPassword, $user['user_password'])) {
                // Validate user data
                $validatedData = $this->validateUserData($data, $user_id, $user);
                
                if (!$validatedData) {
                    return false;
                }

                $username = $validatedData['username'];
                $email = $validatedData['email'];
                $password = $validatedData['password'];
                $user = $validatedData['user'];

                try {

                    if (!empty($username) && $username !== $user['username']) {
                        $sql = "UPDATE Users SET username = :username WHERE id = :id";
                        $stmt = $this->conn->prepare($sql);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                        $stmt->execute();
                    }

                    if (!empty($email) && $email !== $user['email']) {
                        $sql = "UPDATE Users SET email = :email WHERE id = :id";
                        $stmt = $this->conn->prepare($sql);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                        $stmt->execute();
                    }

                    if (!empty($password) && !password_verify($password, $user['user_password'])) {
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "UPDATE Users SET user_password = :passwordHash WHERE id = :id";
                        $stmt = $this->conn->prepare($sql);
                        $stmt->bindParam(':passwordHash', $passwordHash);
                        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                        $stmt->execute();
                    }

                    header("Location: dashboard.php");
                    return true;
                }
                catch (PDOException $e) {
                    $errorMessage = date('Y-m-d H:i:s') . " - Account changing failed: " . $e->getMessage() . "\n";
                    file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
                
                    return false;
                }
            }
            else {
                echo "<p style='color: red;'>Changing data failed: Invalid password</p>";

                $errorMessage = date('Y-m-d H:i:s') . " - Changing data failed: Invalid password\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);

                return false;
            }

        }

        public function deleteUser($data, $user_id) {
            $currentPassword = $data['wachtwoord'] ?? '';

            $user = $this->fetchUserById($user_id);
            if ($user_id !== null && $user === false) {
                echo "<p style='color: red;'>User not found. Cannot update.</p>";
                return false;
            }

            if ($user && password_verify($currentPassword, $user['user_password'])) {
                try {
                    $sql = "DELETE FROM UserGames WHERE user_id = :user_id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $sql = "DELETE FROM Users WHERE id = :id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $message = date('Y-m-d H:i:s') . " - Account deleted successfully\n";
                    file_put_contents($this->logFile, $message, FILE_APPEND);

                    $this->logout();
                    return true;
                }
                catch (PDOException $e) {
                    $errorMessage = date('Y-m-d H:i:s') . " - Account deletion failed: " . $e->getMessage() . "\n";
                    file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
                
                    return false;
                }
            }
            else {
                echo "<p style='color: red;'>Deleting account failed: Invalid password</p>";

                $errorMessage = date('Y-m-d H:i:s') . " - Deleting account failed: Invalid password\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);

                return false;
            }

        }

        public function connectUserGame($user_id, $game_id) {

            try {
                // Connection between user and game check
                $checkSql = "SELECT COUNT(*) FROM UserGames WHERE user_id = :user_id AND game_id = :game_id";
                $checkStmt = $this->conn->prepare($checkSql);
                $checkStmt->bindParam(':user_id', $user_id);
                $checkStmt->bindParam(':game_id', $game_id);
                $checkStmt->execute();

                if ($checkStmt->fetchColumn() > 0) {
                    $message = date('Y-m-d H:i:s') . " - Connection between user and game already exists\n";
                    file_put_contents($this->logFile, $message, FILE_APPEND);

                    return false;
                }

                // Making connection between user and game
                $sql = "INSERT INTO UserGames (user_id, game_id) VALUES (:user_id, :game_id)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':game_id', $game_id);
                $stmt->execute();

                $message = date('Y-m-d H:i:s') . " - Connection between user and game succesful\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);
            
                return true;
            } 

            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Connection between user and game failed: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
            
                return false;
            }

        }

        public function disconnectUserGame($user_id, $game_id) {
            
            try {
                // Disconnecting user and game
                $sql = "DELETE FROM UserGames WHERE user_id = :user_id AND game_id = :game_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':game_id', $game_id);
                $stmt->execute();

                $message = date('Y-m-d H:i:s') . " - Disconnection between user and game succesful\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);
            
                return true;
            } 

            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Disconnection between user and game failed: " . $e->getMessage() . "\n";
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

        public function getUser($user_id) {
            $retrieve_data = $this->fetchUserById($user_id);
            if (!$retrieve_data) {
                return null;
            }

            $message = date('Y-m-d H:i:s') . " - User data retrieved successfully\n";
            file_put_contents($this->logFile, $message, FILE_APPEND);

            return $this->buildUser($retrieve_data);
        }

        public function buildUser($user_data_list) {
            $user = new User();
            $user->set_id($user_data_list['id']);
            $user->set_username($user_data_list['username']);
            $user->set_email($user_data_list['email']);

            return $user;
        }

    }
?>