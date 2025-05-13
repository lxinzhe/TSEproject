<?php
// Include the database connection
include('db_connect.php');

// Get the selected month from the request
$month = isset($_GET['month']) ? $_GET['month'] : date('m');

// Prepare the SQL query to fetch data from the attendance record table
$sql = "SELECT EmployeeID, Date, ClockInTime, ClockOutTime, OvertimeHours, TotalWorkHours FROM attendancerecord WHERE MONTH(Date) = $month";
$result = $conn->query($sql);

// Check if the query returns any results
if ($result->num_rows > 0) {
    $attendance = [
        'present' => [],
        'absent' => [],
        'status' => []
    ];

    // Loop through the results and categorize them
    while ($row = $result->fetch_assoc()) {
        if ($row["ClockInTime"] != NULL && $row["ClockOutTime"] != NULL) {
            // Employee is present
            $attendance['present'][] = $row;
        } else {
            // Employee is absent
            $attendance['absent'][] = $row;
        }

        // For status (can be based on TotalWorkHours or other business logic)
        $status = 'Absent'; // Default status
        if ($row["ClockInTime"] != NULL && $row["ClockOutTime"] != NULL) {
            $status = 'Present';
        } elseif ($row["OvertimeHours"] > 0) {
            $status = 'Late';
        }

        $attendance['status'][] = ['EmployeeID' => $row['EmployeeID'], 'Date' => $row['Date'], 'Status' => $status];
    }

    // Output the data as a JSON response
    echo json_encode($attendance);
} else {
    // If no data is found, return an empty result
    echo json_encode(['present' => [], 'absent' => [], 'status' => []]);
}

// Close the database connection
$conn->close();
?>
