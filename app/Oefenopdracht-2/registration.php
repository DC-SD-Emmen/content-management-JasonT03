<?php

    // Autoloader
    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    $account_manager = new AccountManager();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = $_POST;
        $account_manager->insertData($data);

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie</title>
</head>
<body>

    <h2>Account aanmaken: </h2>

    <form action="" method="POST"><br>

        <label for='gebruikersnaam'>Naam: </label><br>
        <input type="text" name="gebruikersnaam" requierd><br><br>

        <label for='wachtwoord'>Wachtwoord: </label><br>
        <input type="password" name="wachtwoord" requierd><br><br>

        <input type="submit" value="Submit">

    </form>

    <button onclick="window.location.href='index.php'">Login</button>

</body>
</html>