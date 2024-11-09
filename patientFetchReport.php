<?php 
include 'config.php';
include 'db_conn.php';

// SQL query to fetch the most recently created report for the specified patient
$sql = "SELECT r.reportID, r.patientID, r.therapistID, t.therapistName, r.status, r.created_at, r.pdf_path 
        FROM reports r
        JOIN therapist t ON r.therapistID = t.therapistID
        WHERE r.patientID = ?
        ORDER BY r.created_at DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

// Initialize $report and $isReportAvailable variables
$report = null;
$isReportAvailable = false;

// Check if a row was returned
if ($result->num_rows > 0) {
    $report = $result->fetch_assoc();
    $isReportAvailable = ($report['status'] != 'pending' && !empty($report['pdf_path']));
}

$conn->close();
?>
