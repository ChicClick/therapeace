<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include ('../../config.php');
include ('../../db_conn.php');

if (!isset($_SESSION['therapist_id'])) {
    http_response_code(401); // Unauthorized
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$mysqli = $conn;
$therapistID = $_SESSION['therapist_id'];

$sql = "
    SELECT 
        reports.reportID as reportID,
        patient.patientID as patientID, 
        patient.patientName AS patient_name, 
        therapist.therapistName AS therapist_name, 
        patient.image AS image,
        reports.status AS report_status
    FROM reports
    JOIN therapist ON reports.therapistID = therapist.therapistID
    JOIN patient ON reports.patientID = patient.patientID
    WHERE reports.therapistID = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to prepare statement.']);
    exit();
}


$stmt->bind_param("s", $therapistID);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Fetch proress
$proress = [];

while ($row = $result->fetch_assoc()) {
    $proress[] = $row;
}

// Close the statement and database connection
$stmt->close();
$mysqli->close();

// Return JSON response
header("Content-Type: application/json");
echo json_encode($proress);
exit();
?>
