<?php
include 'db_conn.php';

// Get the therapist ID from the request
$therapistId = $_GET['id'];

// SQL query to fetch therapist information based on therapist ID
$sql = "
    SELECT 
        therapistID, 
        therapistName AS therapist_name, 
        specialization,
        phone, 
        datehired,
        gender,
        address,
        email,
        birthday,
        days_available,
        times_available,
        communication,
        flexibility
    FROM therapist
    WHERE therapistID = ?
";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $therapistId); // Ensure consistency with the variable name
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row); // Return data as JSON
} else {
    echo json_encode(['error' => 'therapist not found']); // Return error message
}

$stmt->close();
$conn->close();
?>
