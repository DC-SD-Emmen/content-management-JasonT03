<?php

    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    class GameManager {

        private $conn;
        private $logFile = 'log_file.txt';

        public function __construct() {
            $db = new Database();
            $this->conn = $db->getConnection();
        }
        
        public function insertData($data, $fileName) {

            // Link POST data to variables
            $title = $data['title'];
            $genre = $data['genre']?? [];
            $platform = $data['platform']?? [];
            $developer = $data['developer'];
            $release_year = $data['release_year'];
            $rating = $data['rating'];
            $description = $data['descriptions'];
            $image = $fileName;

            //Filtering and implode
            $title = htmlspecialchars($title);
            $genre_string = implode(",",$genre);
            $platform_string = implode(",",$platform);
            $developer = htmlspecialchars($developer);
            $description = htmlspecialchars($description);
            $image = htmlspecialchars($image);

            //Regex validation
            $title_Regex = "/^[a-zA-Z0-9\s.,'?!]{1,100}$/";
            $developer_Regex = "/^[a-zA-Z\s-]{1,50}$/";
            $release_year_Regex = "/^\d{4}-\d{2}-\d{2}$/";
            $rating_Regex = "/^([1-9](\.\d)?|10(\.0)?)$/";
            $description_Regex = "/^.{1,1000}$/s";

            // Matching regex to variables and adding errors
            $errors = [];

            if (!preg_match($title_Regex, $title)) {
                $errors[] = "Please enter a correct title.";
            }
            if (empty($genre) || !is_array($genre) || count($genre) < 1) {
                $errors[] = "Please select at least one genre.";
            }
            if (empty($genre) || !is_array($genre) || count($genre) > 4) {
                $errors[] = "The max amount of genres is 4";
            }
            if (empty($platform) || !is_array($platform) || count($platform) < 1) {
                $errors[] = "Please select at least one platform.";
            }
            if (!preg_match($developer_Regex, $developer)) {
                $errors[] = "Please enter a correct developer name.";
            }
            if (!preg_match($release_year_Regex, $release_year)) {
                $errors[] = "Please enter a correct release date.";
            }
            if (!preg_match($rating_Regex, $rating)) {
                $errors[] = "Please enter a correct rating.";
            }
            if (!preg_match($description_Regex, $description)) {
                $errors[] = "Please enter a description (max 1000)";
            }

            // Check for errors
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }

            }             
            else {
                try {
                    $sql = "INSERT INTO Games (title, genre, platform, developer, release_year, rating, descriptions, images)
                            VALUES (:title, :genre, :platform, :developer, :release_year, :rating, :descriptions, :images)";
            
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':genre', $genre_string);
                    $stmt->bindParam(':platform', $platform_string);
                    $stmt->bindParam(':developer', $developer);
                    $stmt->bindParam(':release_year', $release_year);
                    $stmt->bindParam(':rating', $rating);
                    $stmt->bindParam(':descriptions', $description);
                    $stmt->bindParam(':images', $image);
                    
                    $stmt->execute();
                    $message = date('Y-m-d H:i:s') . " - Data inserted successfully for game: $title\n";
                    file_put_contents($this->logFile, $message, FILE_APPEND);
    
                    $stmt->closeCursor();
                } 
    
                catch (PDOException $e) {
                    $errorMessage = date('Y-m-d H:i:s') . " - Insert data failed: " . $e->getMessage() . "\n";
                    file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
                    
                }
    
                // JavaScript redirect
                echo "<script>window.location.href = 'index.php';</script>";
                header("Location: index.php");
                exit();
            }

        }

        // Get data from database class

        public function getGames($game_id = null, $user_id = null) {
            try {
                if ($game_id) {
                    $sql = "SELECT * FROM Games WHERE id = :id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(':id', $game_id, PDO::PARAM_INT);
                } 
                else {
                    $sql = "SELECT * FROM Games";
                    $stmt = $this->conn->prepare($sql);
                }
    
                $stmt->execute();
                $retrieve_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $message = date('Y-m-d H:i:s') . " - Data retrieved successfully" . ($game_id ? " for game ID: $game_id" : "") . "\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);

                return $this->buildGameList($retrieve_data);
            } 
            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Data retrieval failed: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
                return [];
            }
        }

        public function getUserGames($user_id) {
            try {
                $sql = "SELECT Games.* FROM Games 
                        INNER JOIN UserGames ON Games.id = UserGames.game_id 
                        WHERE UserGames.user_id = :user_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
        
                $retrieve_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                $message = date('Y-m-d H:i:s') . " - Data retrieved successfully for user ID: $user_id\n";
                file_put_contents($this->logFile, $message, FILE_APPEND);
        
                return $this->buildGameList($retrieve_data);
            } 
            catch (PDOException $e) {
                $errorMessage = date('Y-m-d H:i:s') . " - Data retrieval failed: " . $e->getMessage() . "\n";
                file_put_contents($this->logFile, $errorMessage, FILE_APPEND);
                return [];
            }

        }

        private function buildGameList($game_data_list) {
            $games = [];
            foreach ($game_data_list as $game_data) {
                $game = new Game();
                $game->set_id($game_data['id']);
                $game->set_title($game_data['title']);
                $game->set_genre($game_data['genre']);
                $game->set_platform($game_data['platform']);
                $game->set_developer($game_data['developer']);
                $game->set_release_year($game_data['release_year']);
                $game->set_rating($game_data['rating']);
                $game->set_description($game_data['descriptions']);
                $game->set_image($game_data['images']);
                
                $games[] = $game;
            }
            return $games;
        }

        // Picture upload

        public function fileUpload($file) {
            $targetDir = 'uploads/';
            $allowedExtensions = ['jpg', 'png', 'jpeg', 'gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            $targetRatio = 16 / 9; // Width 16 : Height 9
            $tolerance = 0.05; //Defiation 5%

            if (!isset($file['name']) || empty($file['name'])) {
                return ['error' => 'No file name provided'];
            }
        
            $fileInfo = pathinfo($file['name']);
            $fileExtension = strtolower($fileInfo['extension']);
        
            if (!in_array($fileExtension, $allowedExtensions)) {
                return ['error' => 'Invalid file type'];
            }
        
            if ($file['size'] > $maxFileSize) {
                return ['error' => 'File too large'];
            }
        
            $targetFile = $targetDir. $file['name'];
        
            if (file_exists($targetFile)) {
                echo 'Image allready found! continue with existing image';
                return ['success' => true, 'error' => 'false'];           }
        
            if (!is_uploaded_file($file['tmp_name'])) {
                return ['error' => 'Invalid file'];
            }
        
            $imageSize = getimagesize($file['tmp_name']);
            if (!$imageSize) {
                return ['error' => 'Invalid image'];
            }

            list($width, $height) = $imageSize;

            $actualRatio = $width / $height;
            if (abs($actualRatio - $targetRatio) > $tolerance) {
                return ['error' => 'Image does not match the required 16:9 ratio'];
            }
        
            if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
                return ['error' => 'Upload failed'];
            }
            //als alles goed is gegaan, zetten we in het antwoord: error = 'false'
            return ['success' => true, 'file' => $targetFile, 'error' => 'false'];

        }

    }

?>