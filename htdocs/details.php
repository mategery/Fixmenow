<?php
if (isset($_GET['id'])) {
    try {
        require_once "dbh.inc.php";
        
        $id = $_GET['id'];
        $query = "SELECT * FROM reports WHERE id = :id"; // Feltételezve, hogy van egy 'id' meződ
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $pdo = null;
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
        <h1>A hiba részletes leírása</h1>
        <p><strong>Cím:</strong> <?php echo htmlspecialchars($report['title']); ?></p>
        <p><strong>Részletek:</strong> <?php echo htmlspecialchars($report['description']); ?></p>
        <p><strong>Hely:</strong> <?php echo htmlspecialchars($report['location']); ?></p>
        <p><strong>Létrehozva:</strong> <?php echo htmlspecialchars($report['created_at']); ?></p>
        <p><strong>Jelentő által megadott idő:</strong> <?php echo htmlspecialchars($report['user_time']); ?></p>

        <p><strong>Tagek:</strong> 
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

        <p><strong>Feltöltött képek:</strong></p>
        <?php
        // Képek elérési útvonalának feldolgozása
        $images = explode(',', $report['images']);
        // Ellenőrizzük, hogy az 'images' mező üres-e
        if (empty(trim($report['images']))) {
            echo '<p><strong>Nincsnek feltöltve képek!</strong></p>'; // Üzenet, ha nincsenek képek
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

        <!-- Felugró ablak -->
        <div id="image-popup" class="popup" onclick="closePopup()">
            <span class="close">&times;</span>
            <img class="popup-content" id="popup-img">
            <div id="caption"></div>
        </div>

        <div class="button-group">
            <form action="feladatok.php" method="post">
                <button class="back-button" type="submit">
                    <i class="fas fa-arrow-left"></i>
                    Vissza
                </button>
            </form>

            <!-- Mockup gomb Feladat elvállalásához -->
            <form action="#" method="post">
                <button class="accept-button" type="button">
                    <i class="fas fa-check"></i>
                    Feladat elvállalása
                </button>
            </form>
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
