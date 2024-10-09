<?php
session_start(); // Session indítása

$message = "";
$error = false; 
$issue = "";
$shortIssue = "";
$location = "";
$dateTime = date("Y-m-d\TH:i");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shortIssue = trim($_POST['short_issue']);
    $issue = trim($_POST['issue']);
    $dateTime = $_POST['datetime']; 
    $location = $_POST['location'];

    if (empty($shortIssue) || empty($issue) || empty($dateTime) || ($location === "Egyéb" && empty(trim($_POST['custom_location'])))) {
        $message = "Kérjük, töltsön ki minden mezőt! Ha 'Egyéb' opciót választott, adja meg a helyszínt.";
        $error = true;
    } else {
        // Fájl feltöltés kezelése
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Csak képeket fogadunk el
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    $message = "Hibabejelentése és fájlfeltöltése sikeres!";
                } else {
                    $message = "Sikertelen fájlfeltöltés.";
                    $error = true;
                }
            } else {
                $message = "Csak képeket tölthet fel.";
                $error = true;
            }
        } else {
            $message = "Nem töltött fel képet.";
            $error = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hibabejelentés</title>
    <link rel="stylesheet" href="hibjel.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <h2>Hibabejelentés</h2>

        <?php if ($message): ?>
            <p style="color: <?php echo $error ? 'red' : 'green'; ?>;"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="short_issue">Hiba rövid megnevezése:<span style="color: red;"> *</span></label>
            <input type="text" id="short_issue" name="short_issue" required value="<?php echo htmlspecialchars($shortIssue); ?>">
            
            <label for="issue">Hiba leírása:<span style="color: red;"> *</span></label>
            <textarea id="issue" name="issue" required oninput="autoResize(this)" rows="1"><?php echo htmlspecialchars($issue); ?></textarea>
            
            <label for="datetime">Hiba időpontja:<span style="color: red;"> *</span></label>
            <input type="datetime-local" id="datetime" name="datetime" value="<?php echo $dateTime; ?>" required>

            <label for="location">Helyszín:<span style="color: red;"> *</span></label>
            <select id="location" class="location_dropdown" name="location" required onchange="toggleCustomLocation()">
                <option value="">Válasszon helyszínt</option>
                <option value="Egyéb" <?php echo ($location === "Egyéb") ? 'selected' : ''; ?>>Egyéb</option>
                <option value="Helyszín 1" <?php echo ($location === "Helyszín 1") ? 'selected' : ''; ?>>1. terem</option>
                <option value="Helyszín 2" <?php echo ($location === "Helyszín 2") ? 'selected' : ''; ?>>2. terem</option>
                <option value="Helyszín 3" <?php echo ($location === "Helyszín 3") ? 'selected' : ''; ?>>3. terem</option>
            </select>
            <input type="text" name="location_custom" placeholder="Ha egyéb, adja meg" id="custom_location" value="<?php echo htmlspecialchars($location); ?>" style="display: none;">

            <label for="file">Válassz ki egy képet:</label>
            <input type="file" name="file" id="file" accept="image/*" required>

            <div class="dropdown">
                <button type="button" class="tagbutton" onclick="toggleTags()">Tagek</button>
                <div id="tags_dropdown" style="display:none;">
                    <label><input type="checkbox" name="tags[]" value="Hardware"> Hardware</label>
                    <label><input type="checkbox" name="tags[]" value="Szoftver"> Szoftver</label>
                    <label><input type="checkbox" name="tags[]" value="Hálózat"> Hálózat</label>
                    <label><input type="checkbox" name="tags[]" value="Egyéb"> Egyéb</label>
                </div>
            </div>

            <div class="button-container-send">
                <button class="backbutton" type="button"
                onclick="window.location.href='<?php echo ($_SESSION['rank'] == 'ta') ? 
                'dashboard_ta.php' : ($_SESSION['rank'] == 'rg' ? 'dashboard_rg.php' : 'dashboard_mv.php'); ?>'">
                <i class="fas fa-arrow-left"></i> Vissza
                </button>
                <button type="submit" class="upload-button">Küldés <i class="fas fa-paper-plane"></i></button>
            </div>
        </form>

        <script>
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        function toggleTags() {
            var dropdown = document.getElementById('tags_dropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        function toggleCustomLocation() {
            var locationSelect = document.getElementById('location');
            var customLocationInput = document.getElementById('custom_location');

            if (locationSelect.value === "Egyéb") {
                customLocationInput.style.display = "block";
            } else {
                customLocationInput.style.display = "none";
            }
        }
        </script>
    </div>
</body>
</html>
