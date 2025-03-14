<?php

    // Autoloader
    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    // User Manager
    session_start();
    $user_manager = new UserManager();

    if (isset($_GET['logout'])) {
        $user_manager->logout();
        exit();
    }

    if ($user_manager->isUserLoggedIn() && isset($_GET['wishlist']) && isset($_GET['game_id'])) {
        $user_id = $_SESSION['user_id'];
        $game_id = $_GET['game_id'];

        if ($user_manager->connect_user_game($user_id, $game_id)) {
            header("Location: dashboard.php?wishlist_success=1");
            exit();
        } 
        else {
            header("Location: dashboard.php?wishlist_error=1");
            exit();
        }

    }

    // Game Manager
    $game_manager = new GameManager();
    $games = $game_manager->getGames();

    $game_id = $_GET['id'] ?? null;
    
    if ($game_id) {
        $game_details = $game_manager->getGames($game_id);
        $game = $game_details[0] ?? null;
    }
    else {
        echo "No game ID provided";
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $user_games = $game_manager->getUserGames($user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameDetails</title>

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

        <div class="mainpage-menu">

            <div class="menu-title menu-underline">

                <h1>Game Library</h1>

            </div>

            <div class="menu-functions menu-underline">

                <?php if ($user_manager->isUserLoggedIn()) {?>

                    <button class="home-display menu-buttons" onclick="window.location.href='dashboard.php'">
                        <i class="fa-solid fa-house"></i>
                        Home
                    </button>

                    <button class="add-game menu-buttons" onclick="window.location.href='add_game.php'">
                        <i class="fa-solid fa-gamepad"></i>
                        Add Game
                    </button>

                    <button class="account menu-buttons" onclick="window.location.href='account_settings.php'">
                        <i class="fa-solid fa-user-pen"></i>
                        Account	Settings
                    </button>

                    <button class="logout menu-buttons" onclick="window.location.href='game_details.php?logout'">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Logout
                    </button>

                <?php } else {?>
                    
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
                    
                <?php }?>

            </div>

            <div class="menu-gamelist menu-underline">

                <?php

                    if ($user_manager->isUserLoggedIn()) {

                        foreach ($user_games as $game_item) {

                            echo '<button class="menu-buttons details-button" onclick=window.location.href="game_details.php?id=' . urlencode($game_item->get_id()) . '">';
                                echo '<img class="gamelist-images" src="uploads/' . htmlspecialchars($game_item->get_image()) . '" alt="' . htmlspecialchars($game_item->get_title()) . '">';
                                echo '<div class="gamelist-title">' . htmlspecialchars($game_item->get_title()) . '</div>';
                            echo '</button>';

                        }
                        
                    }
                        
                    else {

                        foreach ($games as $game_item) {

                            echo '<button class="menu-buttons details-button" onclick=window.location.href="game_details.php?id=' . urlencode($game_item->get_id()) . '">';
                                echo '<img class="gamelist-images" src="uploads/' . htmlspecialchars($game_item->get_image()) . '" alt="' . htmlspecialchars($game_item->get_title()) . '">';
                                echo '<div class="gamelist-title">' . htmlspecialchars($game_item->get_title()) . '</div>';
                            echo '</button>';

                        }

                    }

                ?>

            </div>

        </div>

        <div class="mainpage-column">

            <div class="mainpage-header">

                <?php if ($user_manager->isUserLoggedIn()) {?>

                    <button class="home-display header-buttons" onclick="window.location.href='dashboard.php'">
                        <i class="fa-solid fa-house"></i>
                        Home
                    </button>

                    <button class="add-game header-buttons" onclick="window.location.href='add_game.php'">
                        <i class="fa-solid fa-gamepad"></i>
                        Add Game
                    </button>

                    <button class="account header-buttons" onclick="window.location.href='account_settings.php'">
                        <i class="fa-solid fa-user-pen"></i>
                        Account	Settings
                    </button>

                    <button class="logout header-buttons" onclick="window.location.href='game_details.php?logout'">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Logout
                    </button>

                <?php } else {?>

                    <button class="home-display header-buttons" onclick="window.location.href='index.php'">
                        <i class="fa-solid fa-house"></i>
                        Home
                    </button>

                    <button class="add-game header-buttons" onclick="window.location.href='add_game.php'">
                        <i class="fa-solid fa-gamepad"></i>
                        Add Game
                    </button>

                    <button class="login header-buttons" onclick="window.location.href='login.php'">
                        <i class="fa-solid fa-circle-user"></i>
                        Login
                    </button>

                <?php }?>

            </div>

            <div class="mainpage-display">

                <?php if ($game) {

                    echo "<div class='game-details'> ";

                        echo '<img class="details-image" src="uploads/' . htmlspecialchars($game->get_image()) . '">';
                            
                        echo '<div class="details-gameinfo">';

                            echo '<h1 class="details-title">' . htmlspecialchars($game->get_title()) . '</h1>';
                            $genres = explode(",", $game->get_genre());
                            echo '<div class=details-genrebox>';
                                foreach ($genres as $genre) {
                                    echo '<div class="details-genre">' . htmlspecialchars($genre) . '</div>';
                                }
                            echo '</div>';
                            $platforms = explode(",", $game->get_platform());
                            echo '<div class=details-platformbox>';
                                foreach ($platforms as $platform) {
                                    echo '<div class="details-platform">' . htmlspecialchars($platform) . '</div>';
                                }
                            echo '</div>';
                            echo '<h2 class="details-developer">Developer: ' . htmlspecialchars($game->get_developer()) . '</h2>';
                            echo '<h3>Release date: ' . htmlspecialchars($game->get_release_year()) . '</h3>';
                            echo '<h3>Rating: ' . $game->get_rating() . '</h3>';

                        echo '</div>';

                    echo '</div>';

                    echo '<p class="details-description">' . htmlspecialchars($game->get_description()) . '</p>';

                }
                else {
                    echo "No Game Details avaliable";
                }
                ?>

            </div>

        </div>

    </div>


</body>
</html>