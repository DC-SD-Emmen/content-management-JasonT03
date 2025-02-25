<?php
    spl_autoload_register(function ($class_name) { 
        include 'classes/' . $class_name . '.php'; 
    });

    session_start();
    $account_manager = new AccountManager();

    if (!$account_manager->isUserLoggedIn()) {
        header("Location: index.php");
        exit();
    }

    if (isset($_GET['logout'])) {
        $account_manager->logout();
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>

    <h1>Home</h1>

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <button onclick="window.location.href='home.php?logout'">Logout</button>
    <a href='?logout'><button>Logout</button></a>

</body>
</html>