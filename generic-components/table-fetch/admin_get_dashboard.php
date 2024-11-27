<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include ('../../config.php');
include ('../../db_conn.php');

//PROD PATH
// include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/db_conn.php';

if (!isset($_SESSION['username'])) {
    http_response_code(401); // Unauthorized
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$mysqli = $conn;

// Fetch appointments
$appointments = [];
$sqlAppointments = "SELECT 
        therapist.therapistName AS therapist_name, 
        services.serviceName AS service_name, 
        COUNT(appointment.status) AS no_patients_handle
    FROM appointment
    JOIN therapist ON appointment.therapistID = therapist.therapistID
    JOIN services ON appointment.serviceID = services.serviceID
    WHERE appointment.status = 'ongoing'
    GROUP BY therapist.therapistName, services.serviceName";


$stmt = $mysqli->prepare($sqlAppointments);

if ($stmt) {
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

$mysqli->close();

header("Content-Type: application/json");
echo json_encode($appointments);
exit();
?>