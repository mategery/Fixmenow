<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="images/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <?php
        session_start();
        $message = "Üdvözöljük!";
        $error = false;

        if (isset($_SESSION['error'])) {
            $message = $_SESSION['error'];
            $error = true;
            unset($_SESSION['error']);
        }
        ?>

        <div class="header">
            <img src="images/jlogo.png" alt="Logo" class="logo">
            <h2 class="<?php echo $error ? 'error-header' : ''; ?>"><?php echo $message; ?></h2>
        </div>

        <form action="login_handler.php" method="POST">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Belépés</button>
        </form>
    </div>
</body>
</html>
