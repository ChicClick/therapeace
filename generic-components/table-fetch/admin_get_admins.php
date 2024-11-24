<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration and database connection DEV PATH
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
$sqlAppointments = "  SELECT
        admin.adminID as ID,
        CONCAT(admin.firstname, ' ', admin.lastname) AS name,
        admin.phoneNumber AS phoneNumber,
        admin.birthday AS birthday
    FROM admin";

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
