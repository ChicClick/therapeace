<?php
session_start();
require 'db_conn.php';  // Adjust this to your DB connection file

$patientID = $_SESSION['patientID'];  // Make sure session has patient ID
$sql = "SELECT feedback FROM sessionfeedbacknotes WHERE patientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row['feedback'];
}

echo json_encode(['notes' => $notes]);
?>
