<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

header('Content-Type: text/plain');
echo $_SESSION['csrf_token'];
