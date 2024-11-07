<?php
include 'config.php';
include 'db_conn.php'; // Ensure $conn is properly initialized

// Get the form data from the POST request
$appointmentID = isset($_POST['appointmentID']) ? $_POST['appointmentID'] : null;
$selectedDatetime = isset($_POST['selectedDatetime']) ? $_POST['selectedDatetime'] : null;

// Log the incoming datetime
error_log("Selected datetime: " . $selectedDatetime);

// Validate the input data
if (empty($appointmentID) || empty($selectedDatetime)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

// Validate the datetime format: 'Y-m-d H:i:s'
if (!DateTime::createFromFormat('Y-m-d H:i:s', $selectedDatetime)) {
    echo json_encode(['success' => false, 'message' => 'Invalid datetime format.']);
    exit;
}

// Prepare and execute the SQL query to update the appointment schedule
$sql = "UPDATE appointment 
        SET schedule = ? 
        WHERE appointmentID = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param('si', $selectedDatetime, $appointmentID);

// Execute the statement and check the result
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment rescheduled successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating appointment: ' . $stmt->error]);
}

// Debugging: Log the result of the update
error_log("Rows affected: " . $stmt->affected_rows);

$stmt->close();
$conn->close();
?>
