<?php
// Include the database connection
include 'db_connect.php';

// Handle POST request for storing CPD data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data from the POST request
    $employeeName = $_POST['employeeName'] ?? '';  // Get employee name from form
    $programmeName = $_POST['programmeName'] ?? '';  // Get programme name from form
    $clockInTime = $_POST['clockInTime'] ?? '';  // Get clock-in time from form
    $employeeID = $_POST['employeeID'] ?? 1;  // Use employee ID from the form, defaulting to 1
    $date = $_POST['date'] ?? '';  // Get the date from the form

    // Check if all fields are provided
    if (empty($employeeName) || empty($programmeName) || empty($clockInTime) || empty($employeeID)) {
        http_response_code(400);  // Bad Request
        echo "Missing required data.";
        exit;
    }

    // Prepare the SQL statement to insert the data into the 'cpd_record' table
    $stmt = $conn->prepare("INSERT INTO cpd_record (Employee_ID, Name, Date, programme_name, ClockInTime) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        http_response_code(500);  // Internal Server Error
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    // Bind the parameters to the SQL query
    $stmt->bind_param("sssss", $employeeID, $employeeName, $date, $programmeName, $clockInTime);

    // Execute the query
    if ($stmt->execute()) {
        echo "CPD details saved successfully!";
    } else {
        http_response_code(500);  // Internal Server Error
        echo "Execute failed: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
    exit;
}

// Return 405 Method Not Allowed for other HTTP methods
http_response_code(405);
echo "Method not allowed";
exit;
?>
