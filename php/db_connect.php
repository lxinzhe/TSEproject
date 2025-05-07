<?php
// Database connection details
$servername = "localhost";  // your server name, change if necessary
$username = "root";         // your database username
$password = "";             // your database password
$dbname = "attendancesystem"; // the database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
