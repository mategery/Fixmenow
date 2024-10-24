<?php
session_start(); // Session indítása
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üdvözölük!</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <div class="dashboard-container">
    <h1>Üdvözöljük, <?php echo htmlspecialchars($_SESSION['username']); ?>, <?php echo htmlspecialchars($_SESSION['rank']); ?>!</h1>
        <div class="button-container">
            <button class="back-button" onclick="window.location.href='index.php'">
                <i class="fas fa-arrow-left"></i>
                Kijelentkezés
            </button>

            <button class="report-button" onclick="window.location.href='hibjel.php'">
                <i class="fas fa-plus"></i>
                Hiba bejelentése
            </button>

            <form action="feladatok.php" method="post">
                <button class="tasks-button" type="submit">
                    <i class="fas fa-book"></i>
                    Feladatok
                </button>
            </form>
        </div>
    </div>
</body>
</html>
