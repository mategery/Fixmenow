<?php
session_start(); // Ellenőrizzük, hogy a session el van indítva
require_once "dbh.inc.php"; // Adatbázis kapcsolat

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrizzük, hogy van-e 'report_id' az űrlapban
    if (isset($_POST['report_id'])) {
        $report_id = $_POST['report_id'];
        $user_id = $_SESSION['user_id']; // Felhasználó ID a sessionből

        try {
            // SQL lekérdezés a report frissítésére
            $query = "UPDATE reports SET assigned_to = :assigned_to WHERE id = :report_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':assigned_to', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Sikeres frissítés
                $_SESSION['success'] = "A feladat sikeresen elvállalva.";
            } else {
                // Nem történt változás
                $_SESSION['error'] = "Nem történt változás a feladat állapotában.";
            }
        } catch (PDOException $e) {
            // Hiba kezelése
            $_SESSION['error'] = "Hiba történt: " . $e->getMessage();
        }

        // Itt generálunk egy űrlapot, ami automatikusan elküldi a POST kérést
        echo '<form id="redirectForm" action="feladatok.php" method="post">
                <input type="hidden" name="redirect" value="true">
              </form>';
        echo '<script type="text/javascript">document.getElementById("redirectForm").submit();</script>';
        exit();
    } else {
        // Ha a report_id nem létezik
        $_SESSION['error'] = "Érvénytelen kérés.";
        echo '<form id="redirectForm" action="feladatok.php" method="post">
                <input type="hidden" name="redirect" value="true">
              </form>';
        echo '<script type="text/javascript">document.getElementById("redirectForm").submit();</script>';
        exit();
    }
} else {
    // Ha a kérés nem POST
    echo '<form id="redirectForm" action="feladatok.php" method="post">
            <input type="hidden" name="redirect" value="true">
          </form>';
    echo '<script type="text/javascript">document.getElementById("redirectForm").submit();</script>';
    exit();
}
?>
