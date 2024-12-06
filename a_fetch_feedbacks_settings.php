<?php
include 'db_conn.php';

// Query to fetch parent data
$sql = "SELECT * FROM feedbacks_settings WHERE id = 1"; // Adjust according to your table structure
$result = $conn->query($sql);

$settings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[] = $row; // Corrected to add rows to $parents
    }
}

$conn->close();
echo json_encode($settings); // Output the correct array
?>
