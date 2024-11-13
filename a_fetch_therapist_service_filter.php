<?php
require 'db_conn.php';

$conditions = [];
$params = [];
$types = '';

if (isset($_GET['specialization']) && is_string($_GET['specialization'])) {
    $conditions[] = "therapist.specialization = ?";
    $params[] = $_GET['specialization'];
    $types .= 's';
}

if (isset($_GET['days_available']) && is_string($_GET['days_available'])) {
    $conditions[] = "therapist.days_available = ?";
    $params[] = $_GET['days_available'];
    $types .= 's';
}

if (isset($_GET['times_available']) && is_string($_GET['times_available'])) {
    $conditions[] = "therapist.times_available = ?";
    $params[] = $_GET['times_available'];
    $types .= 's';
}

if (isset($_GET['communication']) && is_string($_GET['communication'])) {
    $conditions[] = "therapist.communication = ?";
    $params[] = $_GET['communication'];
    $types .= 's';
}

if (isset($_GET['flexibility']) && is_string($_GET['flexibility'])) {
    $conditions[] = "therapist.flexibility = ?";
    $params[] = $_GET['flexibility'];
    $types .= 's';
}

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
";

if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit();
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

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
