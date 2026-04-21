<?php
session_start();
session_unset();
session_destroy();
// If remember me cookie was set, unset it here in production
header("Location: auth_login.php");
exit;
?>
