<?php
require 'db_conn.php';

$specialization = $_GET['specialization'];

if (!is_string($specialization)) {
    echo json_encode(['error' => 'Invalid specialization parameter']);
    exit();
}

// SQL query to fetch therapist information based on specialization
$sql = "
    SELECT 
        therapist.therapistID, 
        therapist.specialization AS specialization,
        therapist.therapistName AS therapist_name, 
        therapist.phone AS phone, 
        therapist.datehired AS datehired,
        therapist.gender AS gender,
        therapist.address AS address,
        therapist.days_available,
        therapist.times_available,
        therapist.communication,
        therapist.flexibility
    FROM therapist
    WHERE therapist.specialization = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $specialization);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row; 
    }
    echo json_encode($rows); 
} else {
    echo json_encode(['error' => 'Staff not found']);
}

$stmt->close();
$conn->close();
?>