<?php
session_start();
include 'db_conn.php';
header('Content-Type: application/json');

$patientId = $_GET['id'];
if (!isset($_SESSION['therapist_id']) && !isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Therapist ID not set in session']);
    exit;
}
$therapistId = isset( $_SESSION['therapist_id']) ? $_SESSION['therapist_id'] : null;

$response = [];

$sql = "
    SELECT
        patient.patientID,
        patient.patientName AS patient_name,
        patient.parentID as parentID,
        parent.parentName AS parent_name,
        patient.phone AS phone,
        patient.email AS email,
        patient.address AS address,
        patient.birthday AS birthday,
        patient.gender AS gender,
        services.serviceName AS service
    FROM patient
    JOIN parent ON patient.parentID = parent.parentID
    LEFT JOIN services ON patient.serviceID = services.serviceID
    WHERE patient.patientID = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare patient query: ' . $conn->error]);
    exit;
}
$stmt->bind_param("s", $patientId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $response = $result->fetch_assoc();
} else {
    echo json_encode(['error' => 'Patient not found']);
    exit;
}
$notes = [];

if (isset($_SESSION["therapist_id"])) {
    $notesSql = "
    SELECT
        sessionfeedbacknotes.feedbackdate,
        sessionfeedbacknotes.feedback
    FROM sessionfeedbacknotes
    JOIN patient ON sessionfeedbacknotes.patientID = patient.patientID
    WHERE patient.therapistID = ?
";

    $notesStmt = $conn->prepare($notesSql);
    if (!$notesStmt) {
        echo json_encode(['error' => 'Failed to prepare notes query: ' . $conn->error]);
        exit;
    }
    $notesStmt->bind_param("s", $therapistId);
    $notesStmt->execute();
    $notesResult = $notesStmt->get_result();

    if ($notesResult->num_rows > 0) {
        while ($note = $notesResult->fetch_assoc()) {
            $notes[] = $note;
        }
    }

    $notesStmt->close();
}

$response['notes'] = $notes;


$stmt->close();
$conn->close();

// Output the JSON response
echo json_encode($response);
