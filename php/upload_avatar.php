<?php
session_start();
include('db_connect.php');

$response = ["success" => false, "message" => ""];

if (!isset($_SESSION['employee_id'])) {
    $response["message"] = "Not logged in.";
    echo json_encode($response);
    exit;
}

$employee_id = $_SESSION['employee_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response["message"] = "Upload error.";
        echo json_encode($response);
        exit;
    }
    if (!in_array($file['type'], $allowed_types)) {
        $response["message"] = "Only JPG, PNG, GIF allowed.";
        echo json_encode($response);
        exit;
    }
    if ($file['size'] > $max_size) {
        $response["message"] = "File too large (max 2MB).";
        echo json_encode($response);
        exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'avatar_' . $employee_id . '_' . time() . '.' . $ext;
    $target_dir = '../image/';
    $target_path = $target_dir . $new_filename;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        $response["message"] = "Failed to save file.";
        echo json_encode($response);
        exit;
    }

    // Update database
    $avatar_path = 'image/' . $new_filename;
    $sql = "UPDATE employees SET avatar='$avatar_path' WHERE employee_id='$employee_id'";
    if (mysqli_query($conn, $sql)) {
        $response["success"] = true;
        $response["message"] = "Avatar updated.";
        $response["avatar"] = $avatar_path;
    } else {
        $response["message"] = "Database update failed.";
    }
    echo json_encode($response);
    exit;
} else {
    $response["message"] = "No file uploaded.";
    echo json_encode($response);
    exit;
} 