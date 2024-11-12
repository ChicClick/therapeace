<?php
require 'db_conn.php';

$serviceName = $_GET['serviceName'];

if (!is_string($serviceName)) {
    echo json_encode(['error' => 'Invalid serviceName parameter']);
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
        therapist.times_available
    FROM therapist
    WHERE therapist.specialization = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $serviceName);
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
