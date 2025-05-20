<?php 
header('Content-Type: application/json');
include 'db_connect.php';  // adjust path as needed

$today = new DateTime();
$year = (int)$today->format('Y');
$month = (int)$today->format('m');
$day = (int)$today->format('d');

$totalDays = $day;

// Query attendance records up to today
$sql = "SELECT ClockInTime, Date 
        FROM attendancerecord 
        WHERE YEAR(Date) = ? 
          AND MONTH(Date) = ? 
          AND DAY(Date) <= ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $year, $month, $day);
$stmt->execute();
$result = $stmt->get_result();

// To track which days have records
$recordedDays = [];

$presentCount = 0;
$lateCount = 0;

$lateThreshold = new DateTime('09:00:00');

while ($row = $result->fetch_assoc()) {
    $dateStr = $row['Date'];
    $clockIn = $row['ClockInTime'];

    if ($clockIn !== null) {
        $clockInTime = DateTime::createFromFormat('H:i:s', $clockIn);

        if ($clockInTime <= $lateThreshold) {
            $presentCount++;
        } else {
            $lateCount++;
        }
        $recordedDays[$dateStr] = true;
    }
}

// Calculate absent days = totalDays - recorded days
$absentCount = $totalDays - count($recordedDays);
if ($absentCount < 0) $absentCount = 0;

$response = [
    'totalDays' => $totalDays,
    'present' => $presentCount,
    'late' => $lateCount,
    'absent' => $absentCount
];

$stmt->close();
$conn->close();

echo json_encode($response);
