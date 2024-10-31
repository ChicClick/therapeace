<?php
session_start();
include 'db_conn.php'; // This now includes the $mysqli connection object

$mysqli = $conn;

// Check if the patient is logged in
if (!isset($_SESSION['patientID'])) {
    header("Location: patientLogin.php"); // Redirect to login page if session is not set
    exit();
}

$patientID = $_SESSION['patientID']; // Use the patient ID from session

// Get the logged-in patient's name from the session
$patientName = $_SESSION['patientName'];

// Prepare and execute query to fetch appointments for the logged-in patient
$sql = "SELECT a.appointmentID, a.schedule, therapistName
FROM appointment a
JOIN therapist t ON a.therapistID = t.therapistID
WHERE a.patientID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

// Initialize appointments array
$appointments = [];
if ($result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
$appointments[] = $row;
}
}

// Prepare and execute query to fetch sessions for the logged-in patient
$sql = "SELECT s.sessionID, s.sessionDate, s.sessionTime, therapistName
FROM sessions s
JOIN therapist t ON s.therapistID = t.therapistID
WHERE s.patientID = ? AND s.sessionDate <= NOW()";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
die("SQL error: " . $mysqli->error);
}
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

// Initialize sessions array
$sessions = [];
if ($result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
$sessions[] = $row;
}
} else {
echo "No sessions found.";
}


// Close connections
$stmt->close();
$mysqli->close();
?>
