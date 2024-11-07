<?php
include 'db_conn.php'; // Include database connection

// Get patient ID from request
$therapist_id = intval($_GET['therapist_id']); // Get the patient ID from the URL

// SQL query to get specific patient details by ID
$sql = "SELECT * FROM therapist WHERE therapistID = $therapist_id";
$result = $conn->query($sql);

$patient = array();

if ($result->num_rows > 0) {
    // Output data of specific patient
    $patient = $result->fetch_assoc(); // Get patient details
} else {
    // Patient not found
    $patient['error'] = 'Patient not found';
}

// Send JSON response
echo json_encode($patient);

$conn->close(); // Close the connection
?>
 