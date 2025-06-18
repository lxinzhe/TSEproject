<?php
session_start();

// Destroy admin session
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_login_time']);

// Destroy the entire session
session_destroy();

// Redirect to admin login page
header("Location: ../admin_login.html");
exit();
?> 