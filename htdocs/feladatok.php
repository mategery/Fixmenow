<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Relatív idő kiszámítása
    function relativeTime($datetime) {
        $now = new DateTime();  // Aktuális időpont
        $then = new DateTime($datetime);  // Bejelentés időpontja
        $diff = $now->diff($then);

        if ($diff->y >= 1) {
            return $diff->y . " éve";
        } elseif ($diff->m >= 1) {
            return $diff->m . " hónapja";
        } elseif ($diff->d >= 14) {
            $weeks = floor($diff->d / 7);
            return $weeks . " hete";
        } elseif ($diff->d >= 7) {
            return "múlt hét";
        } elseif ($diff->d == 1) {
            return "tegnap";  // Ha pontosan 1 nap telt el
        } elseif ($diff->d > 1) {
            return $diff->d . " napja";
        } elseif ($diff->h >= 1) {
            return $diff->h . " órája";
        } elseif ($diff->i >= 1) {
            return $diff->i . " perce";
        } else {
            return "néhány másodperce";
        }
    }

    try {
        require_once "dbh.inc.php";
    
        $query = "SELECT * from reports ORDER BY created_at DESC";
    
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pdo = null;
        $stmt = null;
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ./index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feladatok</title>
    <link rel="stylesheet" href="feladatok.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <div class="container">
    <?php
        if (empty($results)) {
            echo "<div>";
            echo "<p>Nincs adat</p>";
            echo "</div>";
        } else {
            // Táblázat létrehozása három oszloppal
            if (count($results) > 15) {
                echo '<h1>
                        <span class="back-icon" onclick="window.location.href=\'' . ($_SESSION['rank'] == 'rg' ? 'dashboard_rg.php' : (($_SESSION['rank'] == 'mv' ? 'dashboard_mv.php' : 'index.php'))) . '\'">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                        Feladatok 
                        <span class="mytasks-icon">
                            <i class="fas fa-book"></i>
                        </span>
                      </h1>';
            } else {
                echo '<h1>Feladatok</h1>';
            }
            
            
            echo '<table>';
            echo '<tr>
                    <th>Cím</th>
                    <th>Létrehozva</th>
                    <th>Dátum</th>
                </tr>';

            foreach ($results as $row) {
                // Dátum formázása másodpercek nélkül
                $created_at = new DateTime($row['created_at']);
                $formatted_date = $created_at->format('Y-m-d H:i'); // Csak év, hónap, nap, óra és perc

                echo '<tr onclick="location.href=\'details.php?id=' . $row['id'] . '\'">'; // Az 'id' mező a jelentés azonosítója
                echo '<td>' . htmlspecialchars($row['title']) . '</td>';
                echo '<td>' . relativeTime($row['created_at']) . '</td>';  // Relatív idő oszlop
                echo '<td>' . $formatted_date . '</td>';  // Formázott dátum oszlop (másodpercek nélkül)
                echo '</tr>';
            }
            
            echo '</table>';
        }
        ?>

        <div class="button-group">
            <button class="back-button" type="button" onclick="window.location.href='<?php echo ($_SESSION['rank'] == 'rg') ? 
                'dashboard_rg.php' : ($_SESSION['rank'] == 'mv' ? 'dashboard_mv.php' : 'index.php'); ?>'">
                <i class="fas fa-arrow-left"></i> Vissza
            </button>
            <button class="mytasks-button" type="button">
                Sajátok <i class="fas fa-book"></i>
            </button>
        </div>

    </div>
</body>
</html>
