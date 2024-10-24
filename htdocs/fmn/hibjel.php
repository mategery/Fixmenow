<?php
session_start(); // Session indítása

// Jelenlegi időpont generálása
$currentDateTime = date('Y-m-d\TH:i');
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
        <h1>Hibabejelentés</h1>

        <form action="formhandler.inc.php" method="POST" enctype="multipart/form-data">

            <label for="short_issue">Hiba rövid megnevezése<span style="color: red;">*</span></label>
            <input type="text" id="short_issue" name="short_issue" required>
            
            <label for="issue">Hiba leírása<span style="color: red;">*</span></label>
            <textarea id="issue" name="issue" required oninput="autoResize(this)" rows="1"></textarea>
            
            <label for="datetime">Hiba időpontja<span style="color: red;">*</span></label>
            <input type="datetime-local" id="datetime" name="datetime" value="<?php echo $currentDateTime; ?>" required>

            <label for="location">Helyszín<span style="color: red;">*</span></label>
            <select id="location" class="location_dropdown" name="location" required onchange="toggleCustomLocation()">
                <option value="" disabled selected>Válasszon helyszínt</option>
                <option value="Egyéb">Egyéb</option>
                <option value="Helyszín 1">1. terem</option>
                <option value="Helyszín 2">2. terem</option>
                <option value="Helyszín 3">3. terem</option>
            </select>
            <input type="text" name="location_custom" placeholder="Ha egyéb, adja meg" id="custom_location" style="display: none;" required>



            <label for="images">
                Kép feltöltése
                <span class="optional">(opcionális)</span>
            </label>
            <div class="button-container-img">
                <input type="file" id="images" name="images[]" multiple accept="image/*" onchange="previewImages()" style="display: none;">
                <button type="button" class="upload-button" onclick="document.getElementById('images').click();">Kép kiválasztása <i class="fa-solid fa-arrow-up-from-bracket"></i></button>
                <button type="button" class="clear-button" onclick="removeAllImages()" style="display: none;"><i class="fas fa-trash-alt"></i></button>
            </div>
            <div class="image-preview" id="image_preview"></div>
            
            <label for="images">
                Hiba fajtája
                <span class="optional">(opcionális)</span>
            </label>
            <div class="issue-type">
                <button type="button" class="issue-button" data-value="Szoftver" onclick="toggleSelection(this)">Szoftver</button>
                <button type="button" class="issue-button" data-value="Hardver" onclick="toggleSelection(this)">Hardver</button>
                <button type="button" class="issue-button" data-value="Hálózati" onclick="toggleSelection(this)">Hálózati</button>
                <button type="button" class="issue-button" data-value="Egyéb" onclick="toggleSelection(this)">Egyéb</button>
            </div>

            <!-- Rejtett input mező, ahol a kiválasztott kategóriákat tároljuk -->
            <input type="hidden" id="selected_issues" name="issue_type">



            <div class="button-container-send">
                <button class="backbutton" type="button"
                onclick="window.location.href='<?php echo ($_SESSION['rank'] == '1') ? 
                'dashboard_ta.php' : ($_SESSION['rank'] == '2' ? 'dashboard_rg.php' : 'dashboard_mv.php'); ?>'">
                <i class="fa-solid fa-chevron-left"></i>
                Mégse
                </button>
                <button type="submit" class="send-button">Küldés <i class="fas fa-paper-plane"></i></button>
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
                customLocationInput.required = true; // Kötelezővé tesszük
            } else {
                customLocationInput.style.display = "none";
                customLocationInput.required = false; // Nem kötelező
            }
        }

        function previewImages() {
            const fileInput = document.getElementById('images');
            const previewContainer = document.getElementById('image_preview');
            const clearButton = document.querySelector('.clear-button'); // A törlés gomb
            previewContainer.innerHTML = ''; // Előnézet tisztítása

            // Ha nincsenek kiválasztott képek, rejtsük el a törlés gombot
            if (fileInput.files.length === 0) {
                clearButton.style.display = 'none';
                return;
            }

            clearButton.style.display = 'block'; // Megjelenítjük a törlés gombot

            for (const file of fileInput.files) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    img.classList.add('image-preview-image');

                    const wrapper = document.createElement('div');
                    wrapper.appendChild(img);
                    previewContainer.appendChild(wrapper);
                }

                reader.readAsDataURL(file);
            }
        }

        function removeAllImages() {
            const fileInput = document.getElementById('images');
            const clearButton = document.querySelector('.clear-button'); // A törlés gomb
            fileInput.value = ''; // Törli a fájlokat
            document.getElementById('image_preview').innerHTML = ''; // Előnézet törlése
            clearButton.style.display = 'none'; // Rejtsük el a törlés gombot
        }
        
        //tags
        function toggleSelection(button) {
            button.classList.toggle('selected'); // Állapot megváltoztatása (kiválasztott/nem kiválasztott)

            // Kiválasztott elemek összegyűjtése
            let selectedIssues = [];
            document.querySelectorAll('.issue-button.selected').forEach(function(selectedButton) {
                selectedIssues.push(selectedButton.getAttribute('data-value'));
            });

            // Rejtett input mező frissítése a kiválasztott értékekkel
            document.getElementById('selected_issues').value = selectedIssues.join(',');
        }

        </script>
    </div>
</body>
</html>
