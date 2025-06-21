<?php
require_once "db_connect.php";
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email'])) {
    header('Location: ../forgot_password.html?error=Please+enter+your+email');
    exit;
}
$email = trim($_POST['email']);
file_put_contents(__DIR__ . '/debug_email.txt', "Input email: $email\n", FILE_APPEND);
$sql = "SELECT first_name, last_name, email FROM employees WHERE LOWER(email) = LOWER(?) LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
file_put_contents(__DIR__ . '/debug_email.txt', "Query result: " . print_r($user, true) . "\n", FILE_APPEND);
if (!$user) {
    header('Location: ../forgot_password.html?error=No+user+found+with+that+email');
    exit;
}
$token = bin2hex(random_bytes(16));
$token_hash = hash('sha256', $token);
$expiry = date('Y-m-d H:i:s', time() + 60 * 30); // 30 minutes
$update = $conn->prepare("UPDATE employees SET reset_token_hash=?, reset_token_expires_at=? WHERE email=?");
$update->bind_param('sss', $token_hash, $expiry, $email);
$update->execute();
if ($update->affected_rows) {
    $mail = require __DIR__ . '/mailer.php';
    $mail->setFrom("myxinzhe.lee@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $reset_link = "http://localhost/TSEproject-main/TSEproject-main/reset_password.html?token=$token";
    $mail->isHTML(true);
    $mail->Body = "<h2>Password Reset</h2><p>Dear {$user['first_name']} {$user['last_name']},<br>You can reset your password by clicking the link below. This link is valid for 30 minutes.<br><a href='$reset_link'>$reset_link</a></p>";
    try {
        $mail->send();
        header('Location: ../forgot_password.html?success=1');
        exit;
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/debug_email.txt', "Mailer error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
        header('Location: ../forgot_password.html?error=Failed+to+send+email');
        exit;
    }
} else {
    header('Location: ../forgot_password.html?error=Failed+to+update+token');
    exit;
} 