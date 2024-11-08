<?php
include 'config.php';
include 'db_conn.php';

// SQL query to fetch the most recent report for each therapist
$sql = "
    SELECT r.reportID, r.patientID, r.therapistID, r.status, r.created_at, r.updated_at, r.pdf_path, t.therapistName
    FROM reports r
    LEFT JOIN therapist t ON r.therapistID = t.therapistID
    WHERE r.patientID = ? 
    ORDER BY 
        CASE 
            WHEN r.status = 'pending' THEN r.created_at
            WHEN r.status = 'verified' THEN r.updated_at
            ELSE r.created_at 
        END DESC
    LIMIT 1
";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the report details
if ($result->num_rows > 0) {
    $report = $result->fetch_assoc();
} else {
    $report = null;
}

$conn->close();
?>
