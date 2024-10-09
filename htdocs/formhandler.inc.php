<?php
session_start(); // Session indítása

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $short_issue = $_POST["short_issue"];
    $issue = $_POST["issue"];
    $user_time = $_POST["datetime"];

    // Ellenőrizzük, hogy "Egyéb" van-e kiválasztva
    if ($_POST["location"] == "Egyéb") {
        $location = $_POST["location_custom"];
    } else {
        $location = $_POST["location"];
    }

    // Tegyük a tagek beküldését
    $tags = isset($_POST["tags"]) ? $_POST["tags"] : [];
    $tagsString = implode(',', $tags); // Vesszővel elválasztott string

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
        require_once "dbh.inc.php";

        // SQL beszúrás, beleértve a képek elérési útját és a tageket
        $query = "INSERT INTO reports (title, description, user_time, location, images, tags)
                  VALUES (:title, :description, :user_time, :location, :images, :tags);";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam("title", $short_issue);
        $stmt->bindParam("description", $issue);
        $stmt->bindParam("user_time", $user_time);
        $stmt->bindParam("location", $location);
        $stmt->bindParam("images", $imagePathsString);
        $stmt->bindParam("tags", $tagsString); // Tag bekötése

        $stmt->execute();

        // Kapcsolat lezárása
        $pdo = null;
        $stmt = null;

        //dinamikus login!!
        if ($_SESSION['rank'] == 'ta') {
            header("Location: dashboard_ta.php");
        } elseif ($_SESSION['rank'] == 'rg') {
            header("Location: dashboard_rg.php");
        } else {
            header("Location: dashboard_mv.php");
        }
        die();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ./index.php");
}
