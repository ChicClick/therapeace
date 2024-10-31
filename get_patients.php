<?php
include 'db_conn.php'; // Include database connection

// SQL query to get all patients
$sql = "SELECT patientID, patientName FROM patient";
$result = $conn->query($sql);

$patients = array();

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row; // Add patient to the array
    }
}

// Send JSON response
echo json_encode($patients);

$conn->close(); // Close the connection
?>
