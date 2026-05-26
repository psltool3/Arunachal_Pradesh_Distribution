<?php
session_start();

if (isset($_POST['captcha'])) {
    $_SESSION['captcha'] = $_POST['captcha']; // Store in session
    echo "Stored: " . htmlspecialchars($_SESSION['captcha'], ENT_QUOTES, 'UTF-8');
} else {
    echo "Error: No CAPTCHA received!";
}
?>

