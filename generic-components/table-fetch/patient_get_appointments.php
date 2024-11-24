<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include ('../../config.php');
include ('../../db_conn.php');

if (!isset($_SESSION['patientID'])) {
    http_response_code(401); // Unauthorized
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$mysqli = $conn;
$patientID = $_SESSION['patientID'];

// Fetch appointments
$appointments = [];
$sqlAppointments = "SELECT a.appointmentID, a.schedule, t.therapistID, t.therapistName
                    FROM appointment a
                    JOIN therapist t ON a.therapistID = t.therapistID
                    WHERE a.patientID = ?";
$stmt = $mysqli->prepare($sqlAppointments);

if ($stmt) {
    $stmt->bind_param("s", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $mysqli->error]);
    exit();
}

// Close the database connection
$mysqli->close();

// Return JSON response
header("Content-Type: application/json");
echo json_encode($appointments);
exit();
?>
