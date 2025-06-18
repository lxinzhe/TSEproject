<?php
session_start();

// Admin credentials (in production, these should be stored in database with hashed passwords)
$admin_credentials = [
    'admin' => 'admin123',
    'administrator' => 'admin@2024',
    'superadmin' => 'super123'
];

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Validate input
    if (empty($username) || empty($password)) {
        header("Location: ../admin_login.html?error=required");
        exit();
    }
    
    // Check credentials
    if (isset($admin_credentials[$username]) && $admin_credentials[$username] === $password) {
        // Set admin session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_login_time'] = time();
        
        // Redirect to admin dashboard
        header("Location: admin.php");
        exit();
    } else {
        // Invalid credentials
        header("Location: ../admin_login.html?error=invalid");
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header("Location: ../admin_login.html");
    exit();
}
?> 