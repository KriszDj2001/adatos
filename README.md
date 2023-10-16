# adatos

Login / Logout / Register netről szedett
db.php:
<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'felhasznalonev');
define('DB_PASSWORD', 'jelszo');
define('DB_NAME', 'db_neve');
 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
