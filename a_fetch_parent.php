<?php
include 'db_conn.php';

// Query to fetch parent data
$sql = "SELECT parentID, parentName FROM parent"; // Adjust according to your table structure
$result = $conn->query($sql);

$parents = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $parents[] = $row; // Corrected to add rows to $parents
    }
}

$conn->close();
echo json_encode($parents); // Output the correct array
?>
