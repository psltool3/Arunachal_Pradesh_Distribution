<?php
session_start();
// Generate a random nonce value
$nonceValue = bin2hex(random_bytes(16));
$_SESSION['login_nonce'] = $nonceValue;
echo $nonceValue;
?>
