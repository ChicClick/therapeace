<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_conn.php'; // Include your database connection file

session_start(); // Start the session

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

            // Insert into sessions table with therapistID
            $sessionQuery = "INSERT INTO sessions (patientID, sessionType, sessionDate, therapistID, sessionTime) VALUES (?, ?, ?, ?, ?)";
            $stmtSession = $conn->prepare($sessionQuery);
            if ($stmtSession) {
                $stmtSession->bind_param("sssss", $patientID, $serviceName, $sessionDate, $therapistID, $sessionTime);
                $stmtSession->execute();

                // Get the last inserted sessionID
                $sessionID = $stmtSession->insert_id;

                // Prepare to insert feedback into sessionfeedbacknotes
                $feedbackQuery = "INSERT INTO sessionfeedbacknotes (sessionID, patientID, feedback, feedbackDate) VALUES (?, ?, ?, ?)";
                $stmtFeedback = $conn->prepare($feedbackQuery);
                if ($stmtFeedback) {
                    $stmtFeedback->bind_param("isss", $sessionID, $patientID, $feedback, $sessionDate);
                    if ($stmtFeedback->execute()) {
                        $confirmationMessage = "Feedback submitted successfully.";
                    } else {
                        $confirmationMessage = "Error inserting feedback: " . $stmtFeedback->error;
                    }
                    $stmtFeedback->close(); // Close feedback statement
                } else {
                    $confirmationMessage = "Error preparing feedback statement: " . $conn->error;
                }
                $stmtSession->close(); // Close session statement
            } else {
                $confirmationMessage = "Error preparing session statement: " . $conn->error;
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
    echo "<script>
        alert('$confirmationMessage');
        window.location.href = 'therapist-dashboard.php';
    </script>";
}
?>
