<?php
// Get therapist ID from query parameter
if (!isset($_GET['therapist_id']) || empty($_GET['therapist_id'])) {
    echo json_encode(['error' => 'Therapist ID is required.']);
    exit;
}

$therapist_id = $_GET['therapist_id'];

// Database connection (adjust with your DB credentials)
include 'db_conn.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch booked dates for the therapist
$sql = "SELECT schedule FROM appointment WHERE therapistID = ?";

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $therapist_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch booked dates
$bookedDates = [];
while ($row = $result->fetch_assoc()) {
    $bookedDates[] = $row['schedule'];
}

// Return booked dates as JSON
echo json_encode(['bookedDates' => $bookedDates]);

// Close the connection
$conn->close();
?>
