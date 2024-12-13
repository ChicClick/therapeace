<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';
include 'db_conn.php'; // Include your database connection file

// Check if therapistID is set in the session
if (!isset($_SESSION['therapist_id'])) {
    die("Therapist ID not found in session."); // Stop execution if therapistID is missing
}

$therapistID = $_SESSION['therapist_id'];
$confirmationMessage = ""; // Initialize a variable to hold confirmation status

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $patientID = $_POST['patientID'];
    $serviceID = $_POST['serviceID'];
    $feedback = $_POST['feedback'];
    $sessionDate = $_POST['sessionDate'];
    $sessionTime = $_POST['sessionTime'];

    // Verify if the patientID exists and is assigned to the logged-in therapist
    $checkPatientQuery = "SELECT * FROM patient WHERE patientID = ? AND therapistID = ?";
    $stmtCheckPatient = $conn->prepare($checkPatientQuery);
    $stmtCheckPatient->bind_param("ss", $patientID, $therapistID);
    $stmtCheckPatient->execute();
    $resultCheckPatient = $stmtCheckPatient->get_result();

    // Proceed only if patientID exists and belongs to the logged-in therapist
    if ($resultCheckPatient->num_rows > 0) {
        // Fetch the serviceName based on the serviceID
        $serviceQuery = "SELECT serviceName FROM services WHERE serviceID = ?";
        $stmtService = $conn->prepare($serviceQuery);
        $stmtService->bind_param("i", $serviceID);
        $stmtService->execute();
        $resultService = $stmtService->get_result();

        // Check if the service exists
        if ($resultService->num_rows > 0) {
            $rowService = $resultService->fetch_assoc();
            $serviceName = $rowService['serviceName'];

            // Insert into session_feedbacks table
            $sessionFeedbackQuery = "INSERT INTO session_feedbacks (patientID, sessionType, sessionDate, therapistID, sessionTime, feedback) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtSessionFeedback = $conn->prepare($sessionFeedbackQuery);
            if ($stmtSessionFeedback) {
                $stmtSessionFeedback->bind_param("ssssss", $patientID, $serviceName, $sessionDate, $therapistID, $sessionTime, $feedback);
                if ($stmtSessionFeedback->execute()) {
                    $confirmationMessage = "Feedback submitted successfully.";
                } else {
                    $confirmationMessage = "Error inserting feedback: " . $stmtSessionFeedback->error;
                }
                $stmtSessionFeedback->close(); // Close statement
            } else {
                $confirmationMessage = "Error preparing session feedback statement: " . $conn->error;
            }
        } else {
            $confirmationMessage = "Service not found.";
        }
        $stmtService->close(); // Close service statement
    } else {
        $confirmationMessage = "Patient ID does not exist or does not belong to the logged-in therapist.";
    }

    $stmtCheckPatient->close(); // Close patient check statement
    $conn->close(); // Close the database connection

    // Output the confirmation message and redirect to the therapist dashboard using JavaScript
    header("Location: therapist-dashboard.php?message=" . urlencode($confirmationMessage));
    exit;
}
?>
