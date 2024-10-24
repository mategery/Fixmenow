<?php
if (isset($_GET['id'])) {
    try {
        require_once "dbh.inc.php";
        
        $id = $_GET['id'];
        $query = "SELECT *, reported_by, assigned_to FROM reports WHERE id = :id"; // Hozzáadva az assigned_to mező

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        // Lekérdezés a felhasználó nevének megszerzésére
        $reported_by = $report['reported_by'];
        $query = "SELECT username FROM users WHERE user_id = :reported_by";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':reported_by', $reported_by);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $reported_by_username = htmlspecialchars($user['username']); // Felhasználó neve
        } else {
            $reported_by_username = "Ismeretlen felhasználó"; // Ha nem található a felhasználó
        }
        
        $pdo = null; // Itt zárjuk le a PDO kapcsolatot, miután már nincs szükség rá
        $stmt = null;
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ./index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Részletes leírás</title>
    <link rel="stylesheet" href="details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <div class="container">
        <h1>Részletes leírás</h1>
        <p><strong>Cím:</strong> <?php echo htmlspecialchars($report['title']); ?></p>
        <p><strong>Részletek:</strong> <?php echo htmlspecialchars($report['description']); ?></p>
        <p><strong>Hely:</strong> <?php echo htmlspecialchars($report['location']); ?></p>
        <p><strong>Létrehozva:</strong> <?php echo htmlspecialchars($report['created_at']); ?></p>
        <p><strong>Jelentő által megadott idő:</strong> <?php echo htmlspecialchars($report['user_time']); ?></p>
        <p><strong>Hiba jelentő neve:</strong> <?php echo $reported_by_username; ?></p> <!-- Hiba jelentő neve -->

        <p><strong>Hiba típusa:</strong> 
            <?php 
            // A tagek kiírása
            $tags = explode(',', $report['tags']); // A tageket vessző alapján feldaraboljuk
            if (!empty($tags[0])) { // Ellenőrizzük, hogy van-e legalább egy tag
                foreach ($tags as $tag) {
                    echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span> '; // A tageket span-be tesszük
                }
            } else {
                echo 'Nincsenek tagek.';
            }
            ?>
        </p>

        <p><strong>Feltöltött képek:</strong>
            <?php
            // Képek elérési útvonalának feldolgozása
            $images = explode(',', $report['images']);
            // Ellenőrizzük, hogy az 'images' mező üres-e
            if (empty(trim($report['images']))) {
                echo ' Nincsenek feltöltve képek.'; // Üzenet, ha nincsenek képek
            } else {
                echo '<div class="image-gallery">'; // Csak ha vannak képek, akkor nyissuk meg a galériát
                foreach ($images as $image) {
                    $image = trim($image); // Elágazás a képek közötti felesleges szóközök eltávolítása
                    if (!empty($image)) { // Csak nem üres képeket jelenítünk meg
                        echo '<img src="' . htmlspecialchars($image) . '" alt="Kép" class="report-image" onclick="openPopup(\'' . htmlspecialchars($image) . '\')" />';
                    }
                }
                echo '</div>'; // Galéria lezárása
            }
            ?>
        </p>

        <!-- Felugró ablak -->
        <div id="image-popup" class="popup" onclick="closePopup()">
            <span class="close">&times;</span>
            <img class="popup-content" id="popup-img">
            <div id="caption"></div>
        </div>

        <div class="button-group">
            <form action="<?php echo empty($report['assigned_to']) ? 'feladatok.php' : 'sajat_felad.php'; ?>" method="post">
                <button class="back-button" type="submit">
                    <i class="fas fa-arrow-left"></i>
                    Vissza
                </button>
            </form>

            <?php if (empty($report['assigned_to'])): ?> <!-- Ellenőrizzük, hogy az assigned_to mező üres-e -->
                <form action="assign_task.php" method="post">
                    <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['id']); ?>">
                    <button class="accept-button" type="submit">
                        Feladat elvállalása
                        <i class="fas fa-check"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function openPopup(imageSrc) {
        var popup = document.getElementById("image-popup");
        var popupImg = document.getElementById("popup-img");
        var captionText = document.getElementById("caption");
        
        popup.style.display = "block"; // Felugró ablak megjelenítése
        popupImg.src = imageSrc; // A kattintott kép forrásának beállítása
        captionText.innerHTML = imageSrc; // Kép forrása, mint felirat (opcionális)
    }

    function closePopup() {
        var popup = document.getElementById("image-popup");
        popup.style.display = "none"; // Felugró ablak elrejtése
    }
    </script>
</body>
</html>
