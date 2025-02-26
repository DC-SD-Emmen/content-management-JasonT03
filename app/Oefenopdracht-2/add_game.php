<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameForm</title>

    <link rel="stylesheet" href="styles.css">
    <!-- For icons -->
    <script src="https://kit.fontawesome.com/5c285fdb45.js" crossorigin="anonymous"></script>
    <!-- For Poppins fontsyle -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">

</head>

<body>

    <?php

        spl_autoload_register(function ($class_name) { 
            include 'classes/' . $class_name . '.php'; 
        });
   
        $game_manager = new GameManager();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
            $result = $game_manager->fileUpload($_FILES["file_to_upload"]);

            //is error niet false? dan is er kennelijk een error
            if($result['error'] !== 'false') {
                echo $result['error'];
            } 
            else {
                //dan is error dus wel false, en is er geen error en mag je doorgaan met Insert
                $game_manager->insertData($_POST, $_FILES["file_to_upload"]['name']);
            }
                
        }

        $games = $game_manager->getGames();

    ?>

    <!-- Mainpage -->
    <div class="mainpage-container">

        <!-- Menu -->
        <div class="mainpage-menu">

            <div class="menu-title menu-underline">

                <h1>Game Library</h1>

            </div>

            <div class="menu-functions menu-underline">

                <button class="home-display menu-buttons" onclick="window.location.href='index.php'">
                    <i class="fa-solid fa-house"></i>
                    Home
                </button>
            
                <button class="add-game menu-buttons" onclick="window.location.href='add_game.php'">
                    <i class="fa-solid fa-gamepad"></i>
                    Add Game
                </button>

            </div>

            <div class="menu-gamelist menu-underline">

                <?php

                    foreach ($games as $game) {

                        echo '<button class="menu-buttons details-button" onclick=window.location.href="game_details.php?id=' . urlencode($game->get_id()) . '">';
                            echo '<img class="gamelist-images" src="uploads/' . htmlspecialchars($game->get_image()) . '" alt="' . htmlspecialchars($game->get_title()) . '">';
                            echo '<div class="gamelist-title">' . htmlspecialchars($game->get_title()) . '</div>';
                        echo '</button>';

                    }

                ?>

            </div>

        </div>
        
        <!-- Header and Display -->
        <div class="mainpage-column">
            
            <div class="mainpage-header">

                <button class="home-display header-buttons" onclick="window.location.href='index.php'">
                    <i class="fa-solid fa-house"></i>
                    Home
                </button>
            
                <button class="add-game header-buttons" onclick="window.location.href='add_game.php'">
                    <i class="fa-solid fa-gamepad"></i>
                    Add Game
                </button>

            </div>

            <div class="mainpage-display">

                <!-- Form -->

                <form class="form" action="" method="POST" enctype="multipart/form-data">

                    <label for='title'>Title: </label>
                    <input type="text" name="title">

                    <label for="genre">Genre: </label>
                    <fieldset>

                        <span>
                            <input type="checkbox" id="genre_1" name="genre[]" value="Action">
                            <label for="genre_1"> Action</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_2" name="genre[]" value="Adventure">
                            <label for="genre_2"> Adventure</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_3" name="genre[]" value="Casual">
                            <label for="genre_3"> Casual</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_4" name="genre[]" value="Card&Board">
                            <label for="genre_4"> Card and Board</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_5" name="genre[]" value="Fighting">
                            <label for="genre_5"> Fighting</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_6" name="genre[]" value="Indie">
                            <label for="genre_6"> Indie</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_7" name="genre[]" value="Open-World">
                            <label for="genre_7"> Open World</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_8" name="genre[]" value="Platformer">
                            <label for="genre_8"> Platformer</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_9" name="genre[]" value="Puzzle">
                            <label for="genre_9"> Puzzle</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_10" name="genre[]" value="Racing">
                            <label for="genre_10"> Racing</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_11" name="genre[]" value="Ritme">
                            <label for="genre_11"> Ritme</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_12" name="genre[]" value="RPG">
                            <label for="genre_12"> RPG</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_13" name="genre[]" value="Sandbox">
                            <label for="genre_13"> Sandbox</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_14" name="genre[]" value="Shooters">
                            <label for="genre_14"> Shooters</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_15" name="genre[]" value="Simulation">
                            <label for="genre_15"> Simulation</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_16" name="genre[]" value="Sport">
                            <label for="genre_16"> Sport</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_17" name="genre[]" value="Story">
                            <label for="genre_17"> Story</label>
                        </span>

                        <span>
                            <input type="checkbox" id="genre_18" name="genre[]" value="Strategy">
                            <label for="genre_18"> Strategy</label>
                        </span>

                    </fieldset>

                    <label for="platform">Platform: </label>
                    <fieldset>
                        
                        <span>
                            <input type="checkbox" id="platform_1" name="platform[]" value="playstation">
                            <label for="playstation">playstation</label>
                        </span>

                        <span>
                            <input type="checkbox" id="platform_2" name="platform[]" value="xbox">
                            <label for="xbox">xbox</label>
                        </span>

                        <span>
                            <input type="checkbox" id="platform_3" name="platform[]" value="nintendo_switch">
                            <label for="nintendo_switch">nintendo switch</label>
                        </span>

                        <span>
                            <input type="checkbox" id="platform_4" name="platform[]" value="PC">
                            <label for="PC">PC</label>
                        </span>

                        <span>
                            <input type="checkbox" id="platform_5" name="platform[]" value="Tablet">
                            <label for="Tablet">Tablet</label>
                        </span>

                    </fieldset>                    

                    <label for="developer">Developer: </label>
                    <input type="text" name="developer">

                    <label for="release_year">Release year: </label>
                    <input class="release-date" type="date" name="release_year">

                    <label for="rating">Rating: </label>
                    <input type="range" id="rating" name="rating" min="1.0" max="10.0" step="0.1" 
                        oninput="this.nextElementSibling.value = parseFloat(this.value).toFixed(1)">
                    <output for="rating">5.0</output>

                    <label for="description">Description: </label>
                    <textarea name="descriptions"></textarea>

                    <label for="image">Image: </label>
                    <input type="file" id="file_to_upload" name="file_to_upload">

                    <div class="submit-position">
                        <input class="submit-button" type="submit" value="Submit" name="submit">
                    </div>

                </form>
            
            </div>

        </div>

    </div>

</body>

</html>