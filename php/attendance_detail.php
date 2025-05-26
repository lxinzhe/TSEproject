<?php
include('db_connect.php');

$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year = date('Y'); // Optionally get year from request or default to current year

// Prepare SQL to fetch attendance records for the selected month and year
$sql = "SELECT EmployeeID, Date, ClockInTime, ClockOutTime, OvertimeHours, TotalWorkHours 
        FROM attendancerecord 
        WHERE MONTH(Date) = ? AND YEAR(Date) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$attendance = [
    'present' => [],
    'absent' => [],  // This will remain empty; frontend calculates absent count
    'status' => []
];

while ($row = $result->fetch_assoc()) {
    // Consider records where ClockInTime is not NULL as "Present"
    if ($row['ClockInTime'] !== NULL) {
        $attendance['present'][] = $row;
        
        // Set the status as "Present" if ClockInTime exists (ClockOutTime can be NULL)
        $attendance['status'][] = [
            'EmployeeID' => $row['EmployeeID'],
            'Date' => $row['Date'],
            'Status' => 'Present' // This status is set regardless of ClockOutTime
        ];
    }
    // Records with no ClockInTime are not considered present
    // Absent days are those with no record for the day, handled on frontend
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($attendance);
?>
