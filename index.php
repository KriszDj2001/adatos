<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_GET["id"])) {
    $_SESSION["id"] = $_GET["id"];
}

include "db.php";
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        table, th, td {
            border: 1px solid white;
            border-collapse: collapse;
        }
    </style>
</head>
<body class="bg-stone-950 text-white p-8">
    <a href="logout.php" class="bg-red-500 p-2 my-4 hover:bg-red-400">Kilépés</a>

    <?php
    // Az "id" érték kinyerése a $_SESSION-ből
    $id = $_SESSION["id"];

    // SQL lekérdezés előkészítése
    $query = "SELECT name, username, created_at, admin FROM users WHERE id = $id";

    // Lekérdezés végrehajtása
    $result = mysqli_query($link, $query);

    if ($result) {
        // Ellenőrizzük, hogy van találat
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $isAdmin = $row["admin"];
            
            echo '<table class="w-[500px] mt-4">
                    <tbody>
                        <tr>
                            <td>Név:</td>
                            <td>' . $row["name"] . '</td>
                        </tr>
                        <tr>
                            <td>Felhasználónév:</td>
                            <td>' . $row["username"] . '</td>
                        </tr>
                        <tr>
                            <td>Regisztráció:</td>
                            <td>' . $row["created_at"] . '</td>
                        </tr>
                    </tbody>
                </table>';
        } else {
            echo "Nincs találat az adatbázisban az adott 'id' értékre.";
        }
    } else {
        echo "Hiba a lekérdezés végrehajtása során: " . mysqli_error($link);
    }

    // if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['AdatMentoGomb'])){
        $adat1 = $_POST["adat1"];
        $adat2 = $_POST["adat2"];
        $adat3 = $_POST["adat3"];
        $adat4 = $_POST["adat4"];

        $query = "INSERT INTO userdata (userid, adat1, adat2, adat3, adat4) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, "issss", $id, $adat1, $adat2, $adat3, $adat4);

            if (mysqli_stmt_execute($stmt)) {
                echo "Az adatok sikeresen el lettek mentve!";
            } else {
                echo "Hiba az adatok mentése során: " . mysqli_error($link);
            }
        } else {
            echo "Hiba a lekérdezés előkészítése során: " . mysqli_error($link);
        }
    }
    ?>

    <h2 class="text-xl font-semibold my-4">Adatok Szerkesztése</h2>
    <form action="" method="post">
    <label for="adat1" class="block my-2">Adat 1:</label>
    <input type="text" name="adat1" id="adat1" class="p-2 my-2 bg-stone-800 text-white" required>
    
    <label for="adat2" class="block my-2">Adat 2:</label>
    <input type="text" name="adat2" id="adat2" class="p-2 my-2 bg-stone-800 text-white" required>
    
    <label for="adat3" class="block my-2">Adat 3:</label>
    <input type="text" name="adat3" id="adat3" class="p-2 my-2 bg-stone-800 text-white" required>
    
    <label for="adat4" class="block my-2">Adat 4:</label>
    <input type="text" name="adat4" id="adat4" class="p-2 my-2 bg-stone-800 text-white" required>
    
    <input type="submit" value="Mentés" name="AdatMentoGomb" class="p-2 my-2 bg-green-500 hover:bg-green-400">
    </form>
    


    <h2 class="text-xl font-semibold my-4">Mentett adatok</h2>
    <table class="w-[500px]">
        <thead>
            <tr>
                <th>adat1</th>
                <th>adat2</th>
                <th>adat3</th>
                <th>adat4</th>
                <th>added</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT adat1, adat2, adat3, adat4, added FROM userdata WHERE userid = ? ORDER BY added";

            if ($stmt = mysqli_prepare($link, $query)) {
                mysqli_stmt_bind_param($stmt, "i", $id);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $adat1, $adat2, $adat3, $adat4, $added);

                    $rowCount = 0; // Számláló a találatok számához

                    while (mysqli_stmt_fetch($stmt)) {
                        $rowCount++;
                        echo '<tr>';
                        echo '<td>' . $adat1 . '</td>';
                        echo '<td>' . $adat2 . '</td>';
                        echo '<td>' . $adat3 . '</td>';
                        echo '<td>' . $adat4 . '</td>';
                        echo '<td>' . $added . '</td>';
                        echo '</tr>';
                    }

                    if ($rowCount == 0) {
                        echo '<tr><td colspan="4" class="text-center">Még nem lett adat felvéve.</td></tr>';
                    }
                } else {
                    echo "Hiba a lekérdezés végrehajtása során: " . mysqli_error($link);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Hiba a lekérdezés előkészítése során: " . mysqli_error($link);
            }
            ?>
        </tbody>
    </table>


    <?php if ( $isAdmin == 1 ) : ?>

        <form action="" method="post">
            <select name="getUserData" id="getUserData" class="p-2 my-4 bg-stone-800 text-white">
                <option disabled selected value="">Válassz</option>
                <?php
                $query = "SELECT id, name, username FROM users WHERE admin = 0";

                if ($result = mysqli_query($link, $query)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row["id"] . '">' . $row["name"] . ' - ' . $row["username"] . '</option>';
                    }
                } else {
                    echo "Hiba a lekérdezés végrehajtása során: " . mysqli_error($link);
                }
                ?>
            </select>
            <input id="getUserDataButton" name="getUserDataButton" type="submit" value="Kiválasztás" class="p-2 my-2 bg-green-500 hover:bg-green-400">
        </form>

        <h2 class="text-xl font-semibold my-4">Kiválasztott felhasználó adatai</h2>

        <?php
        if(isset($_POST["getUserData"])) {
            $selectedUserId = $_POST["getUserData"];

            $query = "SELECT adat1, adat2, adat3, adat4, added FROM userdata WHERE userid = ? ORDER BY added";

            if ($stmt = mysqli_prepare($link, $query)) {
                mysqli_stmt_bind_param($stmt, "i", $selectedUserId);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $adat1, $adat2, $adat3, $adat4, $added);

                    if (mysqli_stmt_fetch($stmt)) {
                        echo '<table class="w-[500px]">
                                <thead>
                                    <tr>
                                        <th>adat1</th>
                                        <th>adat2</th>
                                        <th>adat3</th>
                                        <th>adat4</th>
                                        <th>added</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        do {
                            echo '<tr>';
                            echo '<td>' . $adat1 . '</td>';
                            echo '<td>' . $adat2 . '</td>';
                            echo '<td>' . $adat3 . '</td>';
                            echo '<td>' . $adat4 . '</td>';
                            echo '<td>' . $added . '</td>';
                            echo '</tr>';
                        } while (mysqli_stmt_fetch($stmt));
                        echo '</tbody></table>';
                    } else {
                        echo "Nincs találat az adatbázisban ehhez a felhasználóhoz.";
                        echo $selectedUserId;
                    }
                } else {
                    echo "Hiba a lekérdezés végrehajtása során: " . mysqli_error($link);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Hiba a lekérdezés előkészítése során: " . mysqli_error($link);
            }
        }
        ?>




    <?php endif; ?>



    <?php
    // Adatbázis kapcsolat lezárása
    mysqli_close($link);
    ?>
</body>
</html>
