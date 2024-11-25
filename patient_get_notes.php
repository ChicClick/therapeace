<?php
include 'config.php';
include 'db_conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


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

// Check if we're fetching therapists
if (isset($_POST['fetchTherapists'])) {
    $sql = "SELECT DISTINCT t.therapistID, t.therapistName
            FROM therapist t
            JOIN sessions s ON t.therapistID = s.therapistID
            WHERE s.patientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $patientID); // "s" for patientID (string)
    
    if (!$stmt->execute()) {
        echo json_encode([
            'success' => false,
            'error' => 'Query execution failed: ' . $stmt->error
        ]);
        exit();
    }

    $result = $stmt->get_result();

    $therapists = [];
    while ($row = $result->fetch_assoc()) {
        $therapists[] = $row;
    }

    echo json_encode([
        'success' => true,
        'therapists' => $therapists
    ]);
    exit();
}

$sessionID = isset($_POST['sessionID']) ? $_POST['sessionID'] : '';

if ($sessionID && $patientID) {
    // Prepare and execute query to fetch notes based on sessionID and patientID
    $sql = "SELECT s.sessionDate, therapistName, n.feedback, n.feedbackDate
            FROM sessionfeedbacknotes n
            JOIN sessions s ON n.sessionID = s.sessionID
            JOIN therapist t ON s.therapistID = t.therapistID
            WHERE n.sessionID = ? AND n.patientID = ?";
    $stmt = $conn->prepare($sql);
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
$conn->close();
?>
