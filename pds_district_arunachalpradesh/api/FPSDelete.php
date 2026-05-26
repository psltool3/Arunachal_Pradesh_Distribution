<?php

require('../util/Connection.php');
require('../structures/FPS.php');
require('../util/SessionFunction.php');
require('../structures/Login.php');
require('../util/Logger.php');
require('../util/Security.php');
require('../util/Encryption.php');

session_start();

$nonceValue='nonce_value';

if(!SessionCheck()){
die("Unauthorized");
}

/* REQUEST VALIDATION */

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

/* INPUT VALIDATION */

if(

!isset($_POST['username']) ||

!isset($_POST['password']) ||

!isset($_POST['uid'])

){
die("Invalid request");
}

$username=trim($_POST['username']);

if($username===''){
die("Username required");
}

if(strlen($username)>50){
die("Invalid username");
}

/* SESSION MATCH */

if($_SESSION['district_user']!==$username){
die("Session mismatch");
}

/* PASSWORD DECRYPT */

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

/* VERIFY USER */

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

if(!password_verify(
$password,
$row['password']
)){
die("Wrong password");
}

/* DELETE */

$district=$_SESSION["district_district"];

$uid=trim($_POST['uid']);

$FPS=new FPS;

if($uid==="all"){

$query=$FPS->deletealldistrict(
$FPS,
$district
);

$log_name="all";

}
else{

$FPS->setUniqueid($uid);

$query=$FPS->delete($FPS);

/* LOG NAME */

$log_query=$FPS->logname($FPS);

$log_name="unknown";

$log_result=mysqli_query(
$con,
$log_query
);

if(
$log_result &&
$row=$log_result->fetch_assoc()
){

$log_name=$row['name'];

}

}

/* EXECUTE */

if(!mysqli_query($con,$query)){
die("Delete failed");
}

/* LOG */

$filtered=$_POST;

unset(
$filtered['username'],
$filtered['password']
);

writeLog(

"District user -> ".

$_SESSION['district_user'].

" deleted FPS -> ".

$log_name.

" | ".

json_encode($filtered)

);

mysqli_close($con);

header("Location: ../FPS.php");

exit();

?>