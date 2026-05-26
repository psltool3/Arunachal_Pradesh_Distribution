<?php

require('../util/Connection.php');
require('../structures/FPS.php');
require('../util/SessionFunction.php');
require('../structures/Login.php');
require('../util/Logger.php');
require('../util/Security.php');
require('../util/Encryption.php');

$nonceValue = 'nonce_value';

if (!SessionCheck()) {
    return;
}

require('Header.php');

function formatName($name) {
    $name = preg_replace('/[^a-zA-Z0-9_ ]/', '', $name);
    $name = ucwords(strtolower($name));
    return trim($name);
}

function isValidCoordinate($value, $coordinateType) {
    if (!is_numeric($value)) {
        return false;
    }

    $coordinate = floatval($value);

    switch ($coordinateType) {
        case 'latitude':
            return ($coordinate >= -90 && $coordinate <= 90);
        case 'longitude':
            return ($coordinate >= -180 && $coordinate <= 180);
        default:
            return false;
    }
}

function isStringNumber($stringValue) {
    return is_numeric($stringValue);
}

$person = new Login;
$person->setUsername($_POST["username"]);

$Encryption = new Encryption();
$person->setPassword($Encryption->decrypt($_POST["password"], $nonceValue));

if ($_SESSION['user'] != $person->getUsername()) {
    echo "User is logged in with different username and password";
    return;
}

$query = "SELECT * FROM login WHERE username='" . $person->getUsername() . "'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if (!isValidCoordinate($_POST["latitude"], 'latitude') || 
    !isValidCoordinate($_POST["longitude"], 'longitude')) {
    echo "Error : Check Latitude and Longitude Value";
    exit();
}

if (!isStringNumber($_POST["demand"])) {
    echo "Error : Check Demand Value";
    exit();
}

/* ===== FIXED PARSE ERROR HERE ===== */
$dbHashedPassword = $row['password'];

if (password_verify($person->getPassword(), $dbHashedPassword)) {

    if(!isset($_POST["district"]) || empty($_POST["district"])){

    echo "District required";
    exit();

    }

    $district = formatName($_POST["district"]);
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $name = formatName($_POST["name"]);
    $id = $_POST["id"];
    $type = $_POST["type"];
    $demand = $_POST["demand"];
    $uniqueid = $_POST["uniqueid"];
    $active = $_POST["active"];

    $FPS = new FPS;
    $FPS->setUniqueid($uniqueid);
    $FPS->setDistrict($district);
    $FPS->setLatitude($latitude);
    $FPS->setLongitude($longitude);
    $FPS->setName($name);
    $FPS->setId($id);
    $FPS->setType($type);
    $FPS->setDemand($demand);
    $FPS->setActive($active);

    $query_check = $FPS->checkInsert($FPS);
    $query_result = mysqli_query($con, $query_check);
    $numrows = mysqli_num_rows($query_result);

    if ($numrows != 0) {
        $row = mysqli_fetch_assoc($query_result);
        $uniqueid_check = $row["uniqueid"];

        if ($uniqueid != $uniqueid_check) {
            echo "Error : in updating data as FPS id already exist ID: " . $id;
            echo "</br>";
            exit();
        }
    }

    $query = $FPS->update($FPS);
    mysqli_query($con, $query);

    mysqli_close($con);

    echo "<script>window.location.href = '../FPS.php';</script>";
} else {
    echo "Error : Password or Username is incorrect";
}

?>

<?php require('Fullui.php'); ?>
