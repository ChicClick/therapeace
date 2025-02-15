<?php
include 'db_conn.php';

// Get the patient ID from the request
$patientId = $_GET['id'];
$therapistId = $_SESSION['therapist_id'] ?? null;

// SQL query to join the patient and parent tables and fetch the names and other required information
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
        patient.gender AS gender
    FROM patient
    JOIN parent ON patient.parentID = parent.parentID
    WHERE patient.patientID = ?
";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row); // Return data as JSON
} else {
    echo json_encode(['error' => 'Patient not found']); // Return error message
}

$stmt->close();
$conn->close();
?>