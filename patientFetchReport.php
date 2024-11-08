<?php
include 'config.php';
include 'db_conn.php';

// SQL query to fetch the most recent created_at field from the reports table for the specified patient
$sql = "SELECT r.created_at 
        FROM reports r
        WHERE r.patientID = ? 
        ORDER BY r.created_at DESC 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the most recent created_at field
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $createdAt = $row['created_at'];
    echo "Most Recent Created At: " . $createdAt;
} else {
    echo "No reports found.";
}

$conn->close();
?>
