<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if user already clocked in today
    $employeeName = $_GET['employeeName'] ?? '';
    $date = $_GET['date'] ?? '';

    header('Content-Type: application/json');

    if (empty($employeeName) || empty($date)) {
        echo json_encode(['clockedIn' => false]);
        exit;
    }

    // Prepare statement to check if record exists for user and date
    $stmt = $conn->prepare("SELECT COUNT(*) FROM attendancerecord WHERE Name = ? AND Date = ?");
    if ($stmt === false) {
        echo json_encode(['clockedIn' => false]);
        exit;
    }
    $stmt->bind_param("ss", $employeeName, $date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // If count > 0, user already clocked in today
    $clockedIn = ($count > 0);
    echo json_encode(['clockedIn' => $clockedIn]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeName = $_POST['employeeName'] ?? '';
    $clockInTime = $_POST['clockInTime'] ?? '';
    $date = $_POST['date'] ?? '';

    if (empty($employeeName) || empty($clockInTime) || empty($date)) {
        http_response_code(400);
        echo "Missing required data.";
        exit;
    }

    // TEMP: Simulate mapping name to EmployeeID
    $employeeID = 1;

    // Format time properly
    $time = date("H:i:s", strtotime($clockInTime));

    $stmt = $conn->prepare("INSERT INTO attendancerecord (Name, EmployeeID, Date, ClockInTime) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        http_response_code(500);
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("siss", $employeeName, $employeeID, $date, $time);

    if ($stmt->execute()) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Execute failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}

// For other methods, return 405 Method Not Allowed
http_response_code(405);
echo "Method not allowed";
exit;
?>
