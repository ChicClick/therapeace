<?php

include 'config.php';
include 'db_conn.php';

header('Content-Type: application/json'); // Set Content-Type to JSON

$selectedDatetime = isset($_POST['selectedDatetime']) ? $_POST['selectedDatetime'] : null;
$therapist_id = isset($_POST['therapistID']) ? $_POST['therapistID'] : null;

if (empty($selectedDatetime)) {
    echo json_encode(['success' => false, 'message' => 'Selected datetime is required.']);
    exit;
}

if (!DateTime::createFromFormat('Y-m-d H:i:s', $selectedDatetime)) {
    echo json_encode(['error' => true, 'message' => 'Invalid datetime format: ' . $selectedDatetime]);
    exit;
}

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Update the appointment schedule for the specified therapist
$sql = "UPDATE appointment SET schedule = ? WHERE therapistID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param('si', $selectedDatetime, $therapist_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Appointment updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No appointment updated.']);
}

$stmt->close();
$conn->close();
?>
