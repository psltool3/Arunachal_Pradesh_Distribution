<?php

require('../util/Connection.php');
require('../structures/Warehouse.php');
require('../util/SessionFunction.php');
require('../util/Logger.php');
require('../structures/Login.php');
require('../util/Security.php');
require('../util/Encryption.php');

session_start();

$nonceValue='nonce_value';

if(!SessionCheck()){
die("Unauthorized");
}

if($_SERVER['REQUEST_METHOD']!=='POST'){
die("Invalid request");
}

/* CSRF */

if(

empty($_POST['csrf_token']) ||

empty($_SESSION['csrf_token']) ||

!hash_equals(
$_SESSION['csrf_token'],
$_POST['csrf_token']
)

){
die("Invalid CSRF");
}

/* CAPTCHA */

if(

empty($_POST['captchainput']) ||

empty($_SESSION['captcha']) ||

!hash_equals(
$_SESSION['captcha'],
trim($_POST['captchainput'])
)

){
die("Invalid captcha");
}

unset($_SESSION['captcha']);

/* INPUT */

if(

!isset($_POST['username']) ||

!isset($_POST['password']) ||

!isset($_POST['uid'])

){
die("Invalid input");
}

$username=trim($_POST['username']);

if($_SESSION['user']!==$username){
die("Session mismatch");
}

/* PASSWORD */

$Encryption=new Encryption();

$password=$Encryption->decrypt(
$_POST["password"],
$nonceValue
);

/* VERIFY */

$stmt=mysqli_prepare(

$con,

"SELECT password FROM login WHERE username=?"

);

mysqli_stmt_bind_param(
$stmt,
"s",
$username
);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$row=mysqli_fetch_assoc($result);

if(!password_verify(
$password,
$row['password']
)){
die("Wrong password");
}

/* DELETE */

$uid=trim($_POST['uid']);

$Warehouse=new Warehouse;

if($uid==="all"){

$query=$Warehouse->deleteall($Warehouse);

}
else{

$Warehouse->setUniqueid($uid);

$query=$Warehouse->delete($Warehouse);

}

mysqli_query($con,$query);

/* LOG */

$filtered=$_POST;

unset(
$filtered['username'],
$filtered['password']
);

writeLog(

"Warehouse deleted by ".

$_SESSION['user'].

" | ".

json_encode($filtered)

);

mysqli_close($con);

header("Location: ../Warehouse.php");

exit();

?>