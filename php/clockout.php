<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeName = $_POST['employeeName'] ?? '';
    $clockOutTime = $_POST['clockOutTime'] ?? '';
    $date = $_POST['date'] ?? '';

    if (empty($employeeName) || empty($clockOutTime) || empty($date)) {
        http_response_code(400);
        echo "Missing required data.";
        exit;
    }

    // Simulate mapping name to EmployeeID (use session or query database for real applications)
    $employeeID = 1;

    // Get the ClockInTime from the database for the employee
    $stmt = $conn->prepare("SELECT ClockInTime FROM attendancerecord WHERE EmployeeID = ? AND ClockOutTime IS NULL ORDER BY RecordID DESC LIMIT 1");
    $stmt->bind_param("i", $employeeID);
    $stmt->execute();
    $stmt->bind_result($clockInTime);
    $stmt->fetch();
    $stmt->close();

    if (!$clockInTime) {
        echo "No clock-in record found for this employee.";
        exit;
    }

    // Convert ClockInTime and ClockOutTime to DateTime objects
    $clockInTime = DateTime::createFromFormat('H:i:s', $clockInTime);
    $clockOutTime = DateTime::createFromFormat('H:i:s', $clockOutTime);

    // Calculate TotalWorkHours
    $interval = $clockInTime->diff($clockOutTime);
    $totalWorkHours = $interval->h + ($interval->i / 60); // Calculate hours as float

    // Format ClockOutTime for storage
    $formattedClockOutTime = $clockOutTime->format('H:i:s');

    // Update ClockOutTime and TotalWorkHours for the employee's most recent clock-in
    $stmt = $conn->prepare("UPDATE attendancerecord SET ClockOutTime = ?, TotalWorkHours = ? WHERE EmployeeID = ? AND ClockOutTime IS NULL");
    
    // Here we use only 3 bind parameters: ClockOutTime, TotalWorkHours, and EmployeeID
    $stmt->bind_param("sdi", $formattedClockOutTime, $totalWorkHours, $employeeID);

    if ($stmt->execute()) {
        echo "Clock-out time recorded successfully.";
    } else {
        http_response_code(500);
        echo "Error recording clock-out time: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
