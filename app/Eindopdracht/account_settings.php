<?php

    // Autoloader
    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    // User Manager
    session_start();
    
    $user_manager = new UserManager();

    if (!$user_manager->isUserLoggedIn()) {
        header("Location: index.php");
        exit();
    }

    if ($user_manager->isUserLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $user_info = $user_manager->getUser($user_id);
    }

    if (isset($_GET['logout'])) {
        $user_manager->logout();
        exit();
    }

    if (isset($_GET['wishlist']) && isset($_GET['game_id'])) {
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = $_POST;
        $user_id = $_SESSION['user_id'];
        $user_manager->insertData($data, $user_id);

    }

    // Game Manager
    $game_manager = new GameManager();
    $games = $game_manager->getGames();

    $user_id = $_SESSION['user_id'] ?? null;
    $user_games = $game_manager->getUserGames($user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>account settings</title>

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

                <button class="logout menu-buttons" onclick="window.location.href='account_settings.php?logout'">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Logout
                </button>

            </div>

            <div class="menu-gamelist menu-underline">

                <?php

                    foreach ($user_games as $game) {

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

                <!-- <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2> -->
                
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

                <button class="logout header-buttons" onclick="window.location.href='account_settings.php?logout'">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Logout
                </button>

            </div>

            <div class="mainpage-display">
                
                <h2>Changing account information</h2>

                <form class="form" action="" method="POST">

                    <label for='gebruikersnaam'>Name: </label>
                    <input class="form-input" type="text" name="gebruikersnaam" value="<?php echo $user_info ? htmlspecialchars($user_info->get_username()) : ''; ?>">

                    <label for='email'>Email: </label>
                    <input class="form-input" type="email" name="email" value="<?php echo $user_info ? htmlspecialchars($user_info->get_email()) : ''; ?>">

                    <label for='wachtwoord'>Password: </label>
                    <input class="form-input" type="password" name="wachtwoord">

                    <input class="submit-button" type="submit" value="Submit">

                </form>

            </div>

        </div>

    </div>

</body>
</html>