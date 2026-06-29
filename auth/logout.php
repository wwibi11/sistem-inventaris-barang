<?php

session_start();
$_SESSION = array();

session_destroy();

// Clear remember me cookie
setcookie('remember_email', '', time() - 3600, '/');

// Redirect to login
header("Location: login.php");
exit;
?>