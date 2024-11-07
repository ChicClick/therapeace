<?php
include 'config.php';
include 'db_conn.php'; // Database connection

// Get therapistID from the query parameters
$therapistID = $_GET['therapistID'];

// Query to fetch booked dates for the selected therapist
$sql = "SELECT schedule FROM appointment WHERE therapistID = ? AND status = 'ongoing'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $therapistID);
$stmt->execute();
$result = $stmt->get_result();

// Prepare the array of booked dates
$bookedDates = [];
while ($row = $result->fetch_assoc()) {
    // Format the date as YYYY-MM-DD (adjust the format as needed)
    $bookedDates[] = date('Y-m-d', strtotime($row['schedule']));
}

// Return the booked dates as JSON
echo json_encode($bookedDates);

// Close the connection
$stmt->close();
$conn->close();
?>
