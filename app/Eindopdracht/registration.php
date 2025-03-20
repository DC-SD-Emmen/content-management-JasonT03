<?php

    // Autoloader
    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    // User Manager
    $user_manager = new UserManager();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = $_POST;
        $user_manager->insertData($data);
        
    }

    // Game Manager
    $game_manager = new GameManager();
    $games = $game_manager->getGames();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration page</title>

    <link rel="stylesheet" href="styles.css">
    <!-- For icons -->
    <script src="https://kit.fontawesome.com/5c285fdb45.js" crossorigin="anonymous"></script>
    <!-- For Poppins fontsyle -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">

</head>
<body>

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

                <button class="login menu-buttons" onclick="window.location.href='login.php'">
                    <i class="fa-solid fa-circle-user"></i>
                    Login
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
        <div class="mainpage-header"></div>

        <div class="mainpage-display">

            <h2>Account aanmaken: </h2>

            <form class="form" action="" method="POST">

                <label for='gebruikersnaam'>Naam: </label>
                <input class="form-input" type="text" name="gebruikersnaam" requierd>

                <label for='email'>Email: </label>
                <input class="form-input" type="email" name="email" requierd>

                <label for='wachtwoord'>Wachtwoord: </label>
                <input class="form-input" type="password" name="wachtwoord" requierd>

                <div class="submit-position">
                    <input class="submit-button" type="submit" value="Submit">
                </div>

            </form>

            <button class="submit-button" onclick="window.location.href='login.php'">Login</button>

        </div>

    </div>

</body>
</html>