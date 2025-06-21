<?php
require_once "db_connect.php";
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['token']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
    header('Location: ../reset_password.html?error=Please+fill+all+fields');
    exit;
}
$token = $_POST['token'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
if ($new_password !== $confirm_password) {
    header('Location: ../reset_password.html?error=Passwords+do+not+match');
    exit;
}
$token_hash = hash('sha256', $token);
$sql = "SELECT id, reset_token_expires_at FROM employees WHERE reset_token_hash = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    header('Location: ../reset_password.html?error=Invalid+or+expired+token');
    exit;
}
if (strtotime($user['reset_token_expires_at']) < time()) {
    header('Location: ../reset_password.html?error=Token+has+expired');
    exit;
}
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE employees SET password=?, reset_token_hash=NULL, reset_token_expires_at=NULL WHERE id=?");
$update->bind_param('si', $new_hash, $user['id']);
$update->execute();
header('Location: ../php/Userlogin.php?reset=success');
exit; 