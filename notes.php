<?php
include 'db_conn.php';
include 'config.php';

// Get therapist ID from session
$therapist_id = $_SESSION['therapist_id'];

// SQL query to join sessionfeedbacknotes, patient, and services tables, filtered by therapistID
$sql = "
    SELECT
        feedbackID,
        patient.patientID as patientID,
        patient.serviceID as serviceID,
        patient.patientName as patient_name,
        patient.image as image,  
        sessionfeedbacknotes.feedbackdate,
        sessionfeedbacknotes.feedback,
        services.serviceName as service_name
    FROM sessionfeedbacknotes
    JOIN patient ON sessionfeedbacknotes.patientID = patient.patientID
    JOIN services ON patient.serviceID = services.serviceID
    WHERE patient.therapistID = ?";

// Prepare the query to avoid SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $therapist_id); // Bind therapist ID
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to hold the data
$feedbackData = [];

if ($result->num_rows > 0) {
    // Fetch the data for each row
    while ($row = $result->fetch_assoc()) {
        // Extract date in a readable format for display and pass raw date to JS for query
        $formattedDate = date("F j, Y", strtotime($row['feedbackdate']));
        $rawDate = $row['feedbackdate'];
        $uniqueID = uniqid();

        // Add each feedback entry to the array
        $feedbackData[] = [
            'patientID' => $row['patientID'],
            'patient_name' => htmlspecialchars($row['patient_name'], ENT_QUOTES),
            'service_name' => htmlspecialchars($row['service_name'], ENT_QUOTES),
            'image' => $row['image'],
            'feedback_date' => $formattedDate,
            'feedback_raw_date' => $rawDate,
            'feedback' => htmlspecialchars($row['feedback'], ENT_QUOTES),
            'unique_id' => $uniqueID
        ];
    }
} else {
    $feedbackData[] = ['message' => 'No feedback found'];
}

// Return the data as JSON
echo json_encode($feedbackData);

$conn->close();
?>
