<?php
// Include the database connection
include 'db_connect.php';

// Initialize the response array
$response = [];

try {
    // Fetch target hours and category names from cpd_categories
    $query1 = "SELECT category_name, target_hours FROM cpd_categories";
    $result1 = $conn->query($query1);

    // Check if there are any rows returned for the first query
    if ($result1->num_rows > 0) {
        $categories = [];

        // Fetch each row and add it to the categories array
        while ($row = $result1->fetch_assoc()) {
            $categories[] = [
                'category_name' => $row['category_name'],
                'target_hours' => (int)$row['target_hours'], // Ensure target_hours is an integer
                'duration_hours' => 0 // Initialize duration_hours to 0
            ];
        }
    } else {
        // No categories found
        http_response_code(404);
        $response = [
            'message' => 'No categories found in cpd_categories.'
        ];
        echo json_encode($response);
        exit;
    }

    // Fetch duration_hours and category_name from cpd_record and related tables
    $query2 = "
        SELECT 
            cpd_record.duration_hours,
            cpd_categories.category_name
        FROM 
            cpd_record
        INNER JOIN 
            cpd_program ON cpd_record.programme_name = cpd_program.programme_name
        INNER JOIN 
            cpd_categories ON cpd_program.category_id = cpd_categories.category_id
    ";
    $result2 = $conn->query($query2);

    // Check if there are any rows returned for the second query
    if ($result2->num_rows > 0) {
        $records = [];

        // Fetch each row and add it to the records array
        while ($row = $result2->fetch_assoc()) {
            // Accumulate duration_hours for matching category_name
            foreach ($categories as &$category) {
                if ($category['category_name'] == $row['category_name']) {
                    $category['duration_hours'] += (float)$row['duration_hours']; // Add duration_hours
                }
            }
        }

        // Prepare the response data
        $response = [
            'totalRecords' => count($categories),
            'records' => $categories
        ];

        // Set the response code to 200 (OK)
        http_response_code(200);
    } else {
        // No event records found
        http_response_code(404);
        $response = [
            'message' => 'No event records found in cpd_record.'
        ];
    }
} catch (Exception $e) {
    // Handle any errors during the query execution
    http_response_code(500);  // Internal Server Error
    $response = [
        'error' => 'Failed to fetch data from database.',
        'message' => $e->getMessage()
    ];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
