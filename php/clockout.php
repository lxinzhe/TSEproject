<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $employeeName = $_GET['employeeName'] ?? '';
    $date = $_GET['date'] ?? '';

    if (empty($employeeName) || empty($date)) {
        echo json_encode(['clockedOut' => false]);
        exit;
    }

    // Check if user has already clocked out today
    $stmt = $conn->prepare("SELECT COUNT(*) FROM attendancerecord WHERE Name = ? AND Date = ? AND ClockOutTime IS NOT NULL");
    if ($stmt === false) {
        echo json_encode(['clockedOut' => false]);
        exit;
    }
    $stmt->bind_param("ss", $employeeName, $date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    $clockedOut = ($count > 0);
    echo json_encode(['clockedOut' => $clockedOut]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeName = $_POST['employeeName'] ?? '';
    $clockOutTimeRaw = $_POST['clockOutTime'] ?? '';
    $date = $_POST['date'] ?? '';

    if (empty($employeeName) || empty($clockOutTimeRaw) || empty($date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required data.']);
        exit;
    }

    // Simulate employee ID mapping (adjust as needed)
    $employeeID = 1;

    // Get the latest clock-in record without clock out time for this user on this date
    $stmt = $conn->prepare("SELECT RecordID, ClockInTime FROM attendancerecord WHERE Name = ? AND Date = ? AND ClockOutTime IS NULL ORDER BY RecordID DESC LIMIT 1");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ss", $employeeName, $date);
    $stmt->execute();
    $stmt->bind_result($recordID, $clockInTime);
    if (!$stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'No clock-in record found to clock out.']);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Parse times
    $clockInDT = DateTime::createFromFormat('H:i:s', $clockInTime);
    $clockOutDT = DateTime::createFromFormat('H:i:s', $clockOutTimeRaw);
    if (!$clockInDT || !$clockOutDT) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid time format.']);
        exit;
    }

    // Calculate total work hours (decimal)
    $interval = $clockInDT->diff($clockOutDT);
    $totalHours = $interval->h + ($interval->i / 60);

    // Format clock out time
    $clockOutTime = $clockOutDT->format('H:i:s');

    // Update record with clock out time and total work hours
    $stmt = $conn->prepare("UPDATE attendancerecord SET ClockOutTime = ?, TotalWorkHours = ? WHERE RecordID = ?");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("sdi", $clockOutTime, $totalHours, $recordID);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Clock-out time recorded successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
?>
