<?php
session_start(); // Start the PHP session

// Check if the patient is logged in
if (!isset($_SESSION['patientID'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Please log in to view notes'
    ]);
    exit();
}

// Get the logged-in patientID from session
$patientID = $_SESSION['patientID'];

// Include the database connection file
$mysqli = require_once 'db_conn.php';  // This will include and return the database connection

// Get sessionID from POST request
$sessionID = isset($_POST['sessionID']) ? $_POST['sessionID'] : '';

if ($sessionID && $patientID) {
    // Prepare and execute query to fetch notes based on sessionID and patientID
    $sql = "SELECT s.sessionDate, therapistName, n.feedback, n.feedbackDate
            FROM sessionfeedbacknotes n
            JOIN sessions s ON n.sessionID = s.sessionID
            JOIN therapist t ON s.therapistID = t.therapistID
            WHERE n.sessionID = ? AND n.patientID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $sessionID, $patientID); // "is" -> i for sessionID (int), s for patientID (string)
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize notes array
    $notes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row; // Store each feedback entry
        }
    }

    // If feedback exists, return the data
    if (!empty($notes)) {
        echo json_encode([
            'success' => true,
            'schedule' => $notes[0]['sessionDate'] ?? '',
            'therapist' => $notes[0]['therapistName'] ?? '',
            'notes' => array_map(function($note) {
                return '<p>' . htmlspecialchars($note['feedback']) . ' (' . htmlspecialchars($note['feedbackDate']) . ')</p>';
            }, $notes)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No feedback found for this session and patient'
        ]);
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid session ID'
    ]);
}

// Close the database connection
$mysqli->close();
?>
