<?php
// Include the external database connection file
include 'db_conn.php';

// Get the GuestID from the query string
$guestId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// SQL query to fetch guest and child information based on GuestID
$sql = "
    SELECT 
        GuestName AS guest_name, 
        ChildName AS child_name, 
        Age AS child_age
    FROM 
        guest
    WHERE 
        GuestID = ?
";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $guestId);
$stmt->execute();
$result = $stmt->get_result();

// Initialize response array
$response = [];

// Check if any data is found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['guest_name'] = $row['guest_name'];
    $response['child_name'] = $row['child_name'];
    $response['child_age'] = $row['child_age'];
} else {
    $response['guest_name'] = 'N/A';
    $response['child_name'] = 'N/A';
    $response['child_age'] = 'N/A';
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
