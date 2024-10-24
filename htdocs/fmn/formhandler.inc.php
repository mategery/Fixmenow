<?php
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Ha a felhasználó nincs bejelentkezve, irányítsd át a bejelentkezési oldalra
    exit();
}

// Ellenőrizzük, hogy a POST metódust használják-e
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bekérjük az űrlap adatait
    $short_issue = trim($_POST["short_issue"]);
    $issue = trim($_POST["issue"]);
    $user_time = trim($_POST["datetime"]);

    // Ellenőrizzük, hogy "Egyéb" van-e kiválasztva
    $location = ($_POST["location"] == "Egyéb") ? trim($_POST["location_custom"]) : trim($_POST["location"]);

    // Kiválasztott tagek összegyűjtése
    $issueTypes = isset($_POST["issue_type"]) ? explode(',', $_POST["issue_type"]) : [];
    $tagsString = implode(',', $issueTypes); // Vesszővel elválasztott string

    $imagePaths = [];
    
    // Képek feltöltése
    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['images']['name'][$key];
            $file_tmp = $_FILES['images']['tmp_name'][$key];

            // Fájl mentési útvonal
            $uploadDir = 'uploads/'; // A mappa, ahová a képeket mentjük
            $filePath = $uploadDir . basename($file_name);

            // Fájl mentése
            if (move_uploaded_file($file_tmp, $filePath)) {
                $imagePaths[] = $filePath; // Hozzáadjuk a sikeresen feltöltött fájlokat az array-hez
            }
        }
    }

    // Kép elérési útvonalak összefűzése
    $imagePathsString = implode(',', $imagePaths);

    try {
        require_once "dbh.inc.php"; // Adatbázis kapcsolat

        // SQL beszúrás, beleértve a képek elérési útját és a tageket
        $query = "INSERT INTO reports (title, description, user_time, location, images, tags, reported_by)
                  VALUES (:title, :description, :user_time, :location, :images, :tags, :reported_by);";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam("title", $short_issue);
        $stmt->bindParam("description", $issue);
        $stmt->bindParam("user_time", $user_time);
        $stmt->bindParam("location", $location);
        $stmt->bindParam("images", $imagePathsString);
        $stmt->bindParam("tags", $tagsString);
        $stmt->bindParam("reported_by", $_SESSION['user_id']); // A bejelentkezett felhasználó azonosítója

        $stmt->execute();

        // Kapcsolat lezárása
        $pdo = null;
        $stmt = null;

        // Átirányítás a dashboardra a felhasználó rangja alapján
        switch ($_SESSION['rank']) {
            case '1':
                header("Location: dashboard_ta.php");
                break;
            case '2':
                header("Location: dashboard_rg.php");
                break;
            case '3':
                header("Location: dashboard_mv.php");
                break;
            default:
                header("Location: index.php"); // Ismeretlen rang esetén irányítás
                break;
        }
        exit();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: index.php"); // Ha nem POST metódus, irányítás
    exit();
}
?>
