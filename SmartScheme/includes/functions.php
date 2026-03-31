<?php
// includes/functions.php
session_start();

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function generateOtp() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}
?>
