<?php

require('../util/Connection.php');
require('../structures/FPS.php');
require('../util/SessionFunction.php');
require('../structures/Login.php');
require('../util/Logger.php');
require('../util/Security.php');
require('../util/Encryption.php');

// session_start();

$nonceValue='nonce_value';



if(!SessionCheck()){

die("Unauthorized");

}



if($_SERVER['REQUEST_METHOD']!=='POST'){

die("Invalid request");

}


if(

empty($_POST['csrf_token']) ||

empty($_SESSION['csrf_token']) ||

!hash_equals($_SESSION['csrf_token'],$_POST['csrf_token'])

){

die("Invalid CSRF");

}



if(

empty($_POST['captchainput']) ||

empty($_SESSION['captcha']) ||

!hash_equals($_SESSION['captcha'],trim($_POST['captchainput']))

){

die("Invalid captcha");

}

unset($_SESSION['captcha']);



if(

!isset($_POST['username']) ||

!isset($_POST['password']) ||

!isset($_POST['uid'])

){

die("Invalid input");

}

$username=trim($_POST['username']);

if($username===''){

die("Username required");

}

if(strlen($username)>50){

die("Invalid username");

}

if(!preg_match('/^[a-zA-Z0-9@._-]+$/',$username)){

die("Invalid username");

}



$Encryption=new Encryption();

try{

$password=$Encryption->decrypt(
$_POST["password"],
$nonceValue
);

}
catch(Exception $e){

die("Invalid password");

}

if(strlen($password)>255){

die("Invalid password");

}



if($_SESSION['user']!==$username){

die("Session mismatch");

}



$stmt=mysqli_prepare(

$con,

"SELECT password FROM login WHERE username=?"

);

if(!$stmt){

die("Database error");

}

mysqli_stmt_bind_param(

$stmt,

"s",

$username

);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$row=mysqli_fetch_assoc($result);

if(empty($row)){

die("Invalid user");

}

$dbHashedPassword=$row['password'];

if(!password_verify($password,$dbHashedPassword)){

die("Wrong password");

}



$uid=trim($_POST['uid']);

$FPS=new FPS;

if($uid==="all"){

$query=$FPS->deleteall($FPS);

}
else{

if(strlen($uid)>100){

die("Invalid UID");

}

$FPS->setUniqueid($uid);

$query=$FPS->delete($FPS);

}



if(!mysqli_query($con,$query)){

die("Delete failed");

}



$filteredPost=$_POST;

unset(

$filteredPost['username'],

$filteredPost['password']

);

writeLog(

"FPS deleted by -> ".

$_SESSION['user'].

" | Data -> ".

json_encode($filteredPost)

);



mysqli_close($con);

header("Location: ../FPS.php");

exit();

?>