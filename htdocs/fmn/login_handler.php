<?php
session_start();
require_once "dbh.inc.php"; // Adatbázis kapcsolat

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Ellenőrizd, hogy a mezők nem üresek
    if (!empty($username) && !empty($password)) {
        // SQL lekérdezés a felhasználó ellenőrzésére
        $query = "SELECT * FROM users WHERE username = :username AND password = :password";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['user_id']; // Mentsd el a felhasználó id-ját
            $_SESSION['rank'] = $user['group_id']; // Mentsd el a rangot

            // Átirányítás a megfelelő dashboardra
            switch ($user['group_id']) {
                case 1:
                    header("Location: dashboard_ta.php");
                    break;
                case 2:
                    header("Location: dashboard_rg.php");
                    break;
                case 3:
                    header("Location: dashboard_mv.php");
                    break;
                default:
                    $_SESSION['error'] = "Ismeretlen rang!";
                    header("Location: index.php");
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "Hibás felhasználónév vagy jelszó.";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Kérjük, töltse ki az összes mezőt!";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
