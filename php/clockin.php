<?php
include 'db_connect.php';

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
    // In real app, you'd use session or query the database to get EmployeeID by name
    $employeeID = 1;

    // Use correct time format for database
    $time = date("H:i:s", strtotime($clockInTime));

    // Update SQL query to include 'Name'
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
}
?>
