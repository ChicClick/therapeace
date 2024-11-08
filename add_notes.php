<?php
include 'db_conn.php'; // Include your database connection file

session_start(); // Start the session

// Initialize a variable to hold confirmation status
$confirmationMessage = "";

// Assuming therapistID is stored in the session
$therapistID = $_SESSION['therapist_id'];

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
    $stmtCheckPatient->bind_param("ss", $patientID, $therapistID); // Bind patientID and therapistID
    $stmtCheckPatient->execute();
    $resultCheckPatient = $stmtCheckPatient->get_result();

    // Proceed only if patientID exists and belongs to the logged-in therapist
    if ($resultCheckPatient->num_rows > 0) {
        // Fetch the serviceName based on the serviceID
        $serviceQuery = "SELECT serviceName FROM service WHERE serviceID = ?";
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
                        // Set confirmation message
                        $confirmationMessage = "Feedback submitted successfully.";
                    } else {
                        $confirmationMessage = "Error inserting feedback: " . $stmtFeedback->error;
                    }

                    // Close feedback statement
                    $stmtFeedback->close();
                } else {
                    $confirmationMessage = "Error preparing feedback statement: " . $conn->error;
                }

                // Close session statement
                $stmtSession->close();
            } else {
                $confirmationMessage = "Error preparing session statement: " . $conn->error;
            }
        } else {
            $confirmationMessage = "Service not found.";
        }

        // Close service statement
        $stmtService->close();
    } else {
        $confirmationMessage = "Patient ID does not exist or does not belong to the logged-in therapist.";
    }

    // Close patient check statement
    $stmtCheckPatient->close();
    
    // Close the database connection
    $conn->close();
    
    // Output the confirmation message and redirect to the therapist dashboard
    echo "<script>
        alert('$confirmationMessage');
        window.location.href = 'therapist-dashboard.php'; // Redirect to therapist dashboard
    </script>";
}
?>
