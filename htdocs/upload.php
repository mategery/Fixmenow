<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ellenőrizzük, hogy a fájl feltöltése sikeres volt-e
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        // Fájl információk
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];

        // Megengedett fájltípusok
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedFileTypes)) {
            // Fájl mentése
            $destination = 'uploads/' . $fileName;
            if (move_uploaded_file($fileTmpPath, $destination)) {
                echo "A kép sikeresen feltöltve: " . htmlspecialchars($fileName);
            } else {
                echo "Hiba történt a fájl mentésekor.";
            }
        } else {
            echo "Csak JPEG, PNG és GIF formátumú fájlok engedélyezettek.";
        }
    } else {
        echo "Hiba a fájl feltöltésekor.";
    }
} else {
    echo "Érvénytelen kérés.";
}
?>
