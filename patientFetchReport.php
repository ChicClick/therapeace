<?php
include 'config.php';
include 'db_conn.php';

// SQL query to fetch the required fields from the reports table along with therapist name
$sql = "SELECT r.reportID, r.patientID, r.therapistID, t.therapistName, r.status, r.created_at, r.pdf_path 
        FROM reports r
        JOIN therapist t ON r.therapistID = t.therapistID
        WHERE r.patientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the report details from the database as before
if ($result->num_rows > 0) {
    $report = $result->fetch_assoc();
} else {
    $report = null;
}

// Now you can check if the report's status is pending and whether the pdf_path is empty
$isReportAvailable = ($report['status'] != 'pending' && !empty($report['pdf_path']));

$conn->close();
?>
