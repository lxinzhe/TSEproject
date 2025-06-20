<?php
// Include the database connection
include 'db_connect.php';

// SQL query to fetch all CPD program data from the cpd_program table
$query = "SELECT * FROM cpd_program";

// Execute the query
$result = $conn->query($query);

// Check if the query returned any results
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Display the data (you can replace this with your actual desired output format)
        echo "ID: " . $row["id"] . "<br>";
        echo "Programme Name: " . $row["programme_name"] . "<br>";
        echo "Start Date: " . $row["Day_Start"] . "<br>";
        echo "End Date: " . $row["Day_End"] . "<br>";
        echo "<hr>";
    }
} else {
    echo "No records found";
}

// Close the database connection
$conn->close();
?>
