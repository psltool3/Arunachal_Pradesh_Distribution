<?php
require('../util/Connection.php');
require('../structures/Login.php');
require('../util/Encryption.php');
require('../util/Security.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(empty($_POST)){
    die("Something went wrong");
}

if(empty($_SESSION)){
    die("Something went wrong");
}

// echo json_encode($_POST);
// echo json_encode($_SESSION);
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("Something went wrong. Please log in again");
}

if (
    empty($_POST['captchainput']) ||
    empty($_SESSION['captcha']) ||
    $_SESSION['captcha'] !== trim($_POST['captchainput'])
) {
    die("Something went wrong. Please log in again");
}

unset($_SESSION['captcha']);

$person = new Login;
$person->setUsername($_POST["username"]);
$nonceValue = 'nonce_value';

$Encryption = new Encryption();
$person->setPassword($Encryption->decrypt($_POST["password"], $nonceValue));

$query = "SELECT * FROM login WHERE username='".$person->getUsername()."'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if(empty($row)){
	die("Error : Password or Username is incorrect");
}

if ($row['role'] == 'admin') {
		echo "Error: Admins are not allowed to log in here.";
		exit;
}

if ($row["verified"] == 0) {
		echo "Error: Your account needs to be verified. Please contact admin.";
		exit;
}

$dbHashedPassword = $row['password'];
if(password_verify($person->getPassword(), $dbHashedPassword)){

	$count = 1 + $row['count'];
	$uniqueId = uniqid();
	$authToken = md5($uniqueId);
	$currentLoginTime = date("Y-m-d H:i:s");
	
	$queryUpdate = "UPDATE login SET token='$authToken', lastlogin='$currentLoginTime', count='$count' WHERE username='".$person->getUsername()."'";
	mysqli_query($con, $queryUpdate);

	$_SESSION['district_user'] = $person->getUsername();
	$_SESSION['district_password'] = $person->getPassword();
	$_SESSION['district_district'] = $row["role"];
	$_SESSION['district_token'] = $authToken;

	// Close the database connection
	mysqli_close($con);
	echo "<script>window.location.href = '../Home.php';</script>";
} 
else{
    echo "Error : Password or Username is incorrect";
}

?>
<?php require('Fullui.php');  ?>