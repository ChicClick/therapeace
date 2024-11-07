<?php
include 'config.php';
include 'db_conn.php';

// Read raw POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Extract values from the JSON
$appointmentId = $data['appointmentId'];
$selectedDateTime = $data['selectedDateTime'];  // Get the combined date and time

// Convert the selected date and time to a valid datetime format
// The format 'Y-m-d h:i A' represents 12-hour time format with AM/PM
$datetime = DateTime::createFromFormat('Y-m-d h:i A', $selectedDateTime);

if ($datetime === false) {
    // Handle the error if the format is incorrect
    echo json_encode(['success' => false, 'message' => 'Invalid date/time format']);
    exit();
}

// Get the formatted datetime in MySQL-compatible format (YYYY-MM-DD HH:MM:SS)
$newAppointmentDatetime = $datetime->format('Y-m-d H:i:s');

// Prepare SQL statement to update the appointment date and time
$query = "UPDATE appointment SET schedule = ? WHERE appointmentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $newAppointmentDatetime, $appointmentId);

// Execute the query
if ($stmt->execute()) {
    // Prepare the response
    $response = [
        'success' => true,
        'formattedDate' => date('F j, Y', strtotime($selectedDateTime)),
        'timeText' => date('h:i A', strtotime($selectedDateTime))
    ];
} else {
    // In case of failure
    $response = ['success' => false];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$stmt->close();
$conn->close();
?>
