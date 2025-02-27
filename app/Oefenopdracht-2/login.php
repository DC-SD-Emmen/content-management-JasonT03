<?php

    // Autoloader
    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    $user_manager = new UserManager();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $login_data = $_POST;
        $user_manager->login($login_data);

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>

    <link rel="stylesheet" href="styles.css">
    <!-- For icons -->
    <script src="https://kit.fontawesome.com/5c285fdb45.js" crossorigin="anonymous"></script>
    <!-- For Poppins fontsyle -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    
</head>
<body>

    <h2>Login: </h2>

    <form action="" method="POST"><br>

        <label for='gebruikersnaam'>Naam: </label><br>
        <input type="text" name="gebruikersnaam" requierd><br><br>

        <label for='wachtwoord'>Wachtwoord: </label><br>
        <input type="password" name="wachtwoord" requierd><br><br>

        <input type="submit" value="Submit">

    </form>

    <button onclick="window.location.href='registration.php'">registration</button>

</body>
</html>