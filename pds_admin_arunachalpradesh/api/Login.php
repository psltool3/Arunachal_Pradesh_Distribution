<?php
require('../util/Connection.php');
require('../structures/Login.php');
require('../util/Security.php');
require('../util/Encryption.php');

session_start();

$nonceValue = 'nonce_value';

/* ===============================
   REQUEST METHOD VALIDATION
=================================*/
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    die("Invalid request");
}

/* ===============================
   CSRF VALIDATION
=================================*/
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
){
    die("Something went wrong. Please login again");
}

/* ===============================
   CAPTCHA VALIDATION
=================================*/
if (
    empty($_POST['captchainput']) ||
    empty($_SESSION['captcha']) ||
    $_SESSION['captcha'] !== trim($_POST['captchainput'])
){
    die("Invalid captcha");
}

unset($_SESSION['captcha']);

/* ===============================
   INPUT VALIDATION (MAIN FIX)
=================================*/
if(
    !isset($_POST['username']) ||
    !isset($_POST['password']) ||
    !is_string($_POST['username']) ||
    !is_string($_POST['password'])
){
    die("Invalid login request");
}

$username = trim($_POST['username']);

if($username === ''){
    die("Username required");
}

if(strlen($username) > 50){
    die("Invalid username");
}

/* Optional strict username validation */
if(!preg_match('/^[a-zA-Z0-9@._-]+$/',$username)){
    die("Invalid username format");
}

/* ===============================
   PASSWORD DECRYPTION
=================================*/
$Encryption = new Encryption();

try{
    $password = $Encryption->decrypt($_POST["password"], $nonceValue);
}
catch(Exception $e){
    die("Invalid password");
}

if(!is_string($password)){
    die("Invalid password format");
}

if(strlen($password) > 255){
    die("Invalid password");
}

/* ===============================
   LOGIN OBJECT
=================================*/
$person = new Login;
$person->setUsername($username);
$person->setPassword($password);

/* ===============================
   SECURE DATABASE QUERY
=================================*/
$stmt = mysqli_prepare($con,
"SELECT username,password,role,count FROM login WHERE username=?");

if(!$stmt){
    die("Database error");
}

mysqli_stmt_bind_param($stmt,"s",$username);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

if(empty($row)){
    die("Password or Username is incorrect");
}

/* ===============================
   PASSWORD VERIFY
=================================*/
$dbHashedPassword = $row['password'];

if(password_verify($person->getPassword(), $dbHashedPassword)){

    if($row['role']=="admin"){

        session_regenerate_id(true);

        $count = 1 + (int)$row['count'];

        $uniqueId = uniqid('',true);

        $authToken = hash('sha256',$uniqueId);

        $currentLoginTime = date("Y-m-d H:i:s");

        /* ===============================
           SECURE UPDATE QUERY
        =================================*/
        $stmtUpdate = mysqli_prepare($con,
        "UPDATE login 
        SET token=?, lastlogin=?, count=? 
        WHERE username=?");

        if(!$stmtUpdate){
            die("Database error");
        }

        mysqli_stmt_bind_param(
            $stmtUpdate,
            "ssis",
            $authToken,
            $currentLoginTime,
            $count,
            $username
        );

        mysqli_stmt_execute($stmtUpdate);

        /* ===============================
           SESSION SET
        =================================*/
        $_SESSION['user'] = $username;
        $_SESSION['token'] = $authToken;

        mysqli_close($con);

        header("Location: ../Home.php");
        exit();
    }
    else{
        die("Unauthorized role");
    }
}
else{
    die("Password or Username is incorrect");
}

?>