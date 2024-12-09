<?php
header('Content-Type: application/json'); // Set content type to JSON

include 'config.php';
include 'db_conn.php';

$patientID = $_SESSION['patientID'];

// SQL query to fetch the most recent report for each therapist for the specified patient
$sql = "SELECT r.reportID, r.patientID, r.therapistID, t.therapistName, r.status, r.created_at, r.pdf_path
        FROM reports r
        JOIN therapist t ON r.therapistID = t.therapistID
        WHERE r.patientID = ?
        AND r.created_at = (
            SELECT MAX(sub_r.created_at)
            FROM reports sub_r
            WHERE sub_r.therapistID = r.therapistID AND sub_r.patientID = r.patientID
        )
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

$response = [
    'isReportAvailable' => false,
    'reports' => [],
];

// Fetch all reports
while ($report = $result->fetch_assoc()) {
    // Check if the report is more recent than one week
    $reportCreationDate = new DateTime($report['created_at']);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($reportCreationDate);

    // Only include the report if it is less than or equal to 7 days old
    if ($interval->days <= 60) {
        $response['isReportAvailable'] = true;
        $response['reports'][] = [
            'reportID' => $report['reportID'],
            'patientID' => $report['patientID'],
            'therapistID' => $report['therapistID'],
            'therapistName' => $report['therapistName'],
            'status' => $report['status'],
            'created_at' => $report['created_at'],
            'pdf_path' => $report['pdf_path'],
        ];
    }
}

$conn->close();

// Output the JSON response
echo json_encode($response);
