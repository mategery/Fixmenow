<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <div class="dashboard-container">
        <h2>Üdvözöljük mv!</h2>
        <div class="button-container">
            <button class="back-button" onclick="window.location.href='index.php'">
                <i class="fas fa-arrow-left"></i>
                Vissza
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
