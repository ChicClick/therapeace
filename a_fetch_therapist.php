<?php
include 'db_conn.php';

// Get the therapistID from the request
$sql = "SELECT therapistID, therapistName FROM therapist"; // Adjust according to your table structure
$result = $conn->query($sql);

$therapists = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $therapists[] = $row;
    }
}

$conn->close();
echo json_encode($therapists);
?>