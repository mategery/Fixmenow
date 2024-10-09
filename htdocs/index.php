<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="images/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="login.css">
</head>
<body class="<?php echo $error ? 'error-bg' : ''; ?>">
    <div class="login-container">
        <!-- PHP változó az üzenethez -->
        <?php
        session_start(); // Session indítása
        $message = "Üdvözöljük!"; // Alapértelmezett üzenet
        $error = false; // Hiba állapot

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // Csak akkor lép be, ha a mezők nem üresek
            if (!empty($username) && !empty($password)) {
                if ($username === "ta") {
                    $_SESSION['rank'] = 'ta'; // Tanár rang eltárolása
                    header("Location: dashboard_ta.php"); // Átirányít a dashboard_ta.php oldalra
                } elseif ($username === "rg") {
                    $_SESSION['rank'] = 'rg'; // Rendszergazda rang eltárolása
                    header("Location: dashboard_rg.php"); // Átirányít a dashboard_rg.php oldalra
                } elseif ($username === "mv") {
                    $_SESSION['rank'] = 'mv'; // Másik rang eltárolása
                    header("Location: dashboard_mv.php"); // Átirányít a dashboard_mv.php oldalra
                } else {
                    $message = "Adjon meg helyes adatokat!"; // Hibás felhasználónév esetén üzenet váltás
                    $error = true; // Hiba történt
                }
            } else {
                $message = "Kérjük, töltse ki az összes mezőt!";
                $error = true; // Üres mezők esetén is hiba
            }
        }
        ?>

        <!-- Logó hozzáadása és üzenet megjelenítése -->
        <div class="header">
            <img src="images/jlogo.png" alt="Logo" class="logo">
            <h2 class="<?php echo $error ? 'error-header' : ''; ?>"><?php echo $message; ?></h2>
        </div>

        <form action="index.php" method="POST">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Belépés</button>
        </form>
    </div>
</body>
</html>
