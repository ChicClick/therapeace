<?php

include 'db_conn.php';

// Query to get the last therapistID
$sql = "SELECT therapistID FROM therapist ORDER BY therapistID DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Extract the numeric part, increment, and prepend with 'T'
    $lastID = intval(substr($row['therapistID'], 1)) + 1;
    $nextID = "T" . str_pad($lastID, 3, "0", STR_PAD_LEFT);
} else {
    // If no therapist exists, start with T001
    $nextID = "T001";
}

echo json_encode(["therapistID" => $nextID]);
$conn->close();
