<?php
// save_notes.php

// Include your database connection
include 'db_conn.php';

// Start the session to access the logged-in therapist's ID
session_start();

// After validating the therapist's credentials
$_SESSION['therapist_id'] = $therapistId; // Store therapist ID in session


// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);
// Prepare data for insertion
$patientId = $data['patientID']; // Use patient ID from form
$sessionDate = $data['sessionDate'];
$sessionTime = $data['sessionTime'];
$feedback = $data['feedback'];
$feedbackDate = date('Y-m-d'); // Current date

try {
    // Insert session details into the sessions table
    $stmt1 = $conn->prepare("INSERT INTO sessions (patientID, sessionDate, sessionTime, therapistID) VALUES (?, ?, ?, ?)"); // Add therapistID
    $stmt1->execute([$patientId, $sessionDate, $sessionTime, $therapistId]);

    // Insert feedback into the sessionfeedback table
    $stmt2 = $conn->prepare("INSERT INTO sessionfeedbacknotes (feedback, feedbackDate) VALUES (?, ?)"); // Add therapistID
    $stmt2->execute([$feedback, $feedbackDate, $therapistId]);

    // Respond with success
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Respond with error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
