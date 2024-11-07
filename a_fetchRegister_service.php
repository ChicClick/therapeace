<?php
include 'db_conn.php';

// Query to fetch service data
$sql = "SELECT serviceID, serviceName FROM services"; // Adjust according to your table structure
$result = $conn->query($sql);

$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row; // Corrected to add rows to $services array
    }
}

$conn->close();
echo json_encode($services); // Output the correct array
?>
