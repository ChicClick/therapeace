<?php
include 'config.php';
include 'db_conn.php'; // Ensure $conn is properly initialized

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if appointmentID is set in the query string
if (isset($_GET['appointmentID'])) {
    $appointmentID = $_GET['appointmentID'];
    // Log the received appointmentID for debugging (optional, doesn't interfere with the response)
    error_log("Received appointmentID: $appointmentID");
} else {
    // Handle the case where appointmentID is not received
    echo json_encode(['success' => false, 'message' => 'No appointmentID received.']);
    exit();
}

$appointmentID = $_GET['appointmentID'];

// Fetch details of the selected appointment
$sql = "SELECT a.schedule, a.therapistID 
        FROM appointment a 
        WHERE a.appointmentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $appointmentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Appointment not found.']);
    exit();
}

$appointment = $result->fetch_assoc();
$therapistID = $appointment['therapistID'];
$currentSchedule = $appointment['schedule'];

// Prepare response array for available dates and times
$response = ['dates' => []];

// Fetch all existing appointments for the same therapist to determine availability
$sql = "SELECT schedule 
        FROM appointment 
        WHERE therapistID = ? AND appointmentID != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $therapistID, $appointmentID);
$stmt->execute();
$result = $stmt->get_result();

$bookedDates = [];
while ($row = $result->fetch_assoc()) {
    $dateTime = strtotime($row['schedule']);
    $date = date('Y-m-d', $dateTime);
    $time = date('H:i', $dateTime);

    // Group times under their respective dates
    if (!isset($bookedDates[$date])) {
        $bookedDates[$date] = [];
    }
    $bookedDates[$date][] = $time;
}

// Generate dates for the calendar (next 30 days)
for ($i = 0; $i < 30; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    
    // Here, we assume that the therapist's working hours are from 09:00 to 17:00 (adjust as needed)
    // Default available times (you could get these dynamically if needed)
    $availableTimes = [];
    $workStart = strtotime("09:00");
    $workEnd = strtotime("17:00");

    // Populate the available times from 09:00 to 17:00 every hour (you can adjust this to minutes or specific time slots)
    for ($time = $workStart; $time <= $workEnd; $time = strtotime('+1 hour', $time)) {
        $availableTimes[] = date('H:i', $time);
    }

    // Now remove the times that are already booked for this date
    if (isset($bookedDates[$date])) {
        // Remove booked times from available slots
        $availableTimes = array_diff($availableTimes, $bookedDates[$date]);

        if (empty($availableTimes)) {
            // Mark date as unavailable if all slots are booked
            $response['dates'][] = [
                'date' => $date,
                'isAvailable' => false,
                'timeSlots' => []
            ];
        } else {
            // Date has some available times
            $response['dates'][] = [
                'date' => $date,
                'isAvailable' => true,
                'timeSlots' => array_values($availableTimes)
            ];
        }
    } else {
        // Date is fully available (no bookings yet)
        $response['dates'][] = [
            'date' => $date,
            'isAvailable' => true,
            'timeSlots' => $availableTimes
        ];
    }
}

// Set the appropriate Content-Type header
header('Content-Type: application/json');

// Return JSON response
echo json_encode($response);

// Close connections
$stmt->close();
$conn->close();
?>
