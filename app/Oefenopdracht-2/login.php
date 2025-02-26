<?php

    // Autoloader
    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    $account_manager = new AccountManager();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $login_data = $_POST;
        $account_manager->login($login_data);

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
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