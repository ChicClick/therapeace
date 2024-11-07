<?php
include 'db_conn.php'; // Database connection file

// Fetch patient IDs and names
$query = "SELECT patientID, patientName FROM patient"; // Adjust table and column names if needed
$result = $conn->query($query);

// Generate HTML options
$options = "";
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row['patientID']}'>{$row['patientName']}</option>";
    }
}

// Return options as response
echo $options;
?>

