<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['employee_id'])) {
    header('Location: ../edit_profile.html?error=Not+logged+in');
    exit;
}
$employee_id = $_SESSION['employee_id'];
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// 获取当前密码hash
$sql = "SELECT password FROM employees WHERE employee_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user || !password_verify($old_password, $user['password'])) {
    header('Location: ../edit_profile.html?error=Current+password+is+incorrect');
    exit;
}
// 检查新密码一致性
if ($new_password || $confirm_password) {
    if ($new_password !== $confirm_password) {
        header('Location: ../edit_profile.html?error=New+passwords+do+not+match');
        exit;
    }
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE employees SET first_name=?, last_name=?, password=? WHERE employee_id=?");
    $update->bind_param('ssss', $first_name, $last_name, $new_hash, $employee_id);
    $update->execute();
} else {
    $update = $conn->prepare("UPDATE employees SET first_name=?, last_name=? WHERE employee_id=?");
    $update->bind_param('sss', $first_name, $last_name, $employee_id);
    $update->execute();
}
header('Location: ../edit_profile.html?success=1');
exit; 