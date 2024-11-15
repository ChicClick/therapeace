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

// SQL query to fetch booked dates for the therapist, including days and times from the therapist table
$sql = "
    SELECT appointment.schedule, therapist.days_available, therapist.times_available 
    FROM therapist
    LEFT JOIN appointment ON appointment.therapistID = therapist.therapistID
    WHERE therapist.therapistID = ?
";

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $stmt->error);
}

// Bind the parameter and execute the query
$stmt->bind_param("s", $therapist_id);
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

$result = $stmt->get_result();
if (!$result) {
    die("Error fetching result: " . $stmt->error);
}

$bookedDates = [];
$blockedDates = null;
$blockedTimes = null;

// Process the result
while ($row = $result->fetch_assoc()) {
    // Get blocked dates and times from the therapist table
    if ($blockedDates === null && $blockedTimes === null) {
        $blockedDates = $row['days_available'];
        $blockedTimes = $row['times_available'];
    }

    // Collect booked dates from the appointment table if they exist
    if (!is_null($row['schedule'])) {
        $bookedDates[] = $row['schedule'];
    }
}

// Return booked dates, blocked dates, and blocked times as JSON
echo json_encode([
    'bookedDates' => $bookedDates,
    'blockedDates' => $blockedDates ?? [],
    'blockedTimes' => $blockedTimes ?? []
]);

// Close the connection
$conn->close();
?>
