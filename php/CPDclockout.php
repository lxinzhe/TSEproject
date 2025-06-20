<?php
// Include the database connection
include 'db_connect.php';

// Function to calculate the duration in hours (integer)
function calculate_duration($clockInTime, $clockOutTime) {
    // Create DateTime objects for both times
    $clockInDT = DateTime::createFromFormat('H:i:s', $clockInTime);
    $clockOutDT = DateTime::createFromFormat('H:i:s', $clockOutTime);

    if (!$clockInDT || !$clockOutDT) {
        return 0; // Return 0 if there's an error with time format
    }

    // Calculate the difference between clock-in and clock-out time
    $interval = $clockInDT->diff($clockOutDT);
    
    // Return the total duration in hours as an integer
    return (int)($interval->h + ($interval->i / 60));  // hours + minutes converted to hours
}

// Handle POST request for storing CPD data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data from the POST request
    $employeeName = $_POST['employeeName'] ?? '';  // Get employee name from form
    $programmeName = $_POST['programmeName'] ?? '';  // Get programme name from form
    $clockOutTimeRaw = $_POST['clockOutTime'] ?? '';  // Get clock-out time from form
    $employeeID = $_POST['employeeID'] ?? 1;  // Use employee ID from the form, defaulting to 1
    $date = $_POST['date'] ?? '';  // Get the date from the form

    // Check if all fields are provided
    if (empty($employeeName) || empty($programmeName) || empty($clockOutTimeRaw) || empty($employeeID) || empty($date)) {
        http_response_code(400);  // Bad Request
        echo "Missing required data.";
        exit;
    }

    // Start a transaction to ensure consistency
    $conn->begin_transaction();

    try {
        // First, fetch the clock-in time from the database
        $stmt1 = $conn->prepare("SELECT ClockInTime FROM cpd_record WHERE Employee_ID = ? AND Date = ? AND programme_name = ? AND ClockOutTime IS NULL LIMIT 1");
        $stmt1->bind_param("sss", $employeeID, $date, $programmeName);
        $stmt1->execute();
        $stmt1->bind_result($clockInTime);
        if (!$stmt1->fetch()) {
            throw new Exception("No clock-in record found to clock out.");
        }
        $stmt1->close();

        // Calculate the duration in hours using the calculate_duration function
        $durationHours = calculate_duration($clockInTime, $clockOutTimeRaw);

        // Update the cpd_record table with clock-out time and duration_hours
        $stmt2 = $conn->prepare("UPDATE cpd_record SET ClockOutTime = ?, duration_hours = ? WHERE Employee_ID = ? AND Date = ? AND programme_name = ?");
        $stmt2->bind_param("sdsss", $clockOutTimeRaw, $durationHours, $employeeID, $date, $programmeName);

        if (!$stmt2->execute()) {
            throw new Exception("Failed to update cpd_record table: " . $stmt2->error);
        }

        // Now, update the cpd_categories table with the target_hours (if needed)
        $stmt3 = $conn->prepare("UPDATE cpd_categories 
                                SET target_hours = target_hours + ? 
                                WHERE category_name = (SELECT category_name FROM cpd_program WHERE programme_name = ?)");
        $stmt3->bind_param("ds", $durationHours, $programmeName);

        if (!$stmt3->execute()) {
            throw new Exception("Failed to update cpd_categories table: " . $stmt3->error);
        }

        // Commit the transaction
        $conn->commit();

        echo "CPD clock-out time saved successfully!";
    } catch (Exception $e) {
        // Rollback transaction if there is any error
        $conn->rollback();
        http_response_code(500);  // Internal Server Error
        echo "Error: " . $e->getMessage();
    }

    // Close the statement and connection
    $stmt2->close();
    $stmt3->close();
    $conn->close();
    exit;
}

// Return 405 Method Not Allowed for other HTTP methods
http_response_code(405);
echo "Method not allowed";
exit;
?>
