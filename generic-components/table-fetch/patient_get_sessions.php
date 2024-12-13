<?php
session_start();

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

$sessions = [];
$sqlSessions = "SELECT s.sessionID, s.sessionDate, s.sessionTime, t.therapistID, t.therapistName
                FROM session_feedbacks s
                JOIN therapist t ON s.therapistID = t.therapistID
                WHERE s.patientID = ? AND s.sessionDate <= NOW()";
$stmt = $mysqli->prepare($sqlSessions);

if ($stmt) {
    $stmt->bind_param("s", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => $mysqli->error]);
    exit();
}

// Close the database connection
$mysqli->close();

// Return JSON response
header("Content-Type: application/json");
echo json_encode($sessions);
exit();
?>
