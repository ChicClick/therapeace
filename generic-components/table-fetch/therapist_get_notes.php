<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include ('../../config.php');
include ('../../db_conn.php');

if (!isset($_SESSION['therapist_id'])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$mysqli = $conn;
$therapistID = $_SESSION['therapist_id'];

$sql = "
        SELECT
            patient.patientID as patientID,
            patient.image as image, 
            patient.patientName AS patient_name,  
            sessionfeedbacknotes.feedbackdate,
            sessionfeedbacknotes.feedback 
        FROM sessionfeedbacknotes
        JOIN patient ON sessionfeedbacknotes.patientID = patient.patientID
        WHERE patient.therapistID = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare statement.']);
    exit();
}

$stmt->bind_param("s", $therapistID);
$stmt->execute();
$result = $stmt->get_result();

$notes = [];

while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}

$stmt->close();
$mysqli->close();

// Return JSON response
header("Content-Type: application/json");
echo json_encode($notes);
exit();
?>
