<?php
header('Content-Type: application/json');
include 'db_connect.php';  // adjust path as needed

$today = new DateTime();
$year = (int)$today->format('Y');
$month = (int)$today->format('m');
$day = (int)$today->format('d');

// Calculate total days so far in current month (e.g., today = 17 means totalDays = 17)
$totalDays = $day;

// Prepare SQL to get attendance for current month and year, up to today
$sql = "SELECT ClockInTime, ClockOutTime, Date 
        FROM attendancerecord 
        WHERE YEAR(Date) = ? 
          AND MONTH(Date) = ? 
          AND DAY(Date) <= ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $year, $month, $day);
$stmt->execute();
$result = $stmt->get_result();

$presentCount = 0;
$lateCount = 0;

// Threshold for late clock-in: 09:00:00
$lateThreshold = new DateTime('09:00:00');

while ($row = $result->fetch_assoc()) {
    $clockIn = $row['ClockInTime'];
    $clockOut = $row['ClockOutTime'];

    if ($clockIn !== null && $clockOut !== null) {
        $presentCount++;
        $clockInTime = DateTime::createFromFormat('H:i:s', $clockIn);
        if ($clockInTime > $lateThreshold) {
            $lateCount++;
        }
    }
}

// Calculate absent days (current month so far - present - late)
$absentCount = $totalDays - $presentCount - $lateCount;
if ($absentCount < 0) $absentCount = 0;

$response = [
    'totalDays' => $totalDays,
    'present' => $presentCount,
    'late' => $lateCount,
    'absent' => $absentCount
];

// Close connections
$stmt->close();
$conn->close();

echo json_encode($response);
