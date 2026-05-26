<?php
require('../util/Connection.php');
require('../structures/Login.php');
require('../util/SessionFunction.php');
require ('../util/Encryption.php');
$nonceValue = 'nonce_value';

if(!SessionCheck()){
	return;
}

require('Header.php');

$person = new Login;
$person->setUsername($_POST["username"]);
$Encryption = new Encryption();
$person->setPassword($Encryption->decrypt($_POST["password"], $nonceValue));

if($_SESSION['user']!=$person->getUsername()){
    echo "User is logged in with a different username and password";
    return;
}
$query = "SELECT * FROM login WHERE username='".$person->getUsername()."'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

// Check if the username exists and verify the password using password_verify
$dbHashedPassword = $row['password'];
if (password_verify($person->getPassword(), $dbHashedPassword)) {
    
    $person = new Login;
    $person->setUsername($_POST["newusername"]);
    $person->setPassword($_POST["newpassword"]);
    $person->setRole($_POST["district"]);
    $uid = uniqid();
	
	

    // Hash the new password before inserting it into the database
    $hashedPassword = password_hash($person->getPassword(), PASSWORD_DEFAULT);

    // Check if the new username already exists
    $query = "SELECT * FROM login WHERE username='".$person->getUsername()."'";
    $result = mysqli_query($con, $query);
    $numrows = mysqli_num_rows($result);

    if($numrows == 1){
        echo "Error : Username already exists";
    } else {
        // Insert the new user with the hashed password
        $query1 = "INSERT INTO login (username, password, uid, role, verified) 
                    VALUES ('".$person->getUsername()."', '".$hashedPassword."', '$uid', '".strtolower($person->getRole())."', '1')";
        mysqli_query($con, $query1);
        mysqli_close($con);
		$filteredPost = $_POST;
		unset($filteredPost['username'], $filteredPost['password']);
		// writeLog("User ->" ." User Add ->". $_SESSION['user'] . "| Requested JSON -> " . json_encode($filteredPost). " | " . $person->getUsername());
        echo "<script>window.location.href = '../Userdata.php';</script>";
    }

} else {
    // Password is incorrect
    echo "Error : Password or Username is incorrect";
    return;
}
?>
<?php require('Fullui.php'); ?>
