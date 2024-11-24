<?php
session_start();
include 'db_conn.php';
header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Missing or invalid report ID']);
    exit;
}

$reportID = $_GET['id'];

$response = [];

$sql = "SELECT * FROM reports WHERE reportID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare query: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $reportID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $response = $result->fetch_assoc();
} else {
    echo json_encode(['error' => 'Report not found']);
    exit;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
