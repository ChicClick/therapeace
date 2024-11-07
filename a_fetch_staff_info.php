<?php
include 'db_conn.php';

// Get the staff ID from the request
$staffId = $_GET['id'];

// SQL query to fetch staff information based on staff ID
$sql = "
    SELECT 
        staff.staffID, 
        staff.staffName AS staff_name, 
        staff.position AS position,
        staff.phoneNumber AS phone, 
        staff.datehired AS datehired,
        staff.gender AS gender,
        staff.address AS address
    FROM staff
    WHERE staff.staffID = ?
";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staffId); // Ensure consistency with the variable name
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row); // Return data as JSON
} else {
    echo json_encode(['error' => 'Staff not found']); // Return error message
}

$stmt->close();
$conn->close();
?>
