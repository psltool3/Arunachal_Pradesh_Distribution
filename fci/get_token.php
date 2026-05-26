<?php
session_start();

// Check if CSRF token exists in session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a new CSRF token
    echo "Stored: " . htmlspecialchars($_SESSION['captcha'], ENT_QUOTES, 'UTF-8');
}


echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
?>
