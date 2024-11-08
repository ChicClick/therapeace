<?php
include 'config.php'; 
include 'db_conn.php'; 

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$messageDisplay = ''; // Initialize a variable for the message to be shown on the front end

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $therapistId = $_POST['therapist_id'] ?? '';

    if (empty($therapistId)) {
        $messageDisplay = 'Therapist ID is required.';
    } else {
        $query = "SELECT email FROM therapist WHERE therapistID = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $therapistId);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            $stmt->close();

            if ($email) {
                $resetToken = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $updateQuery = "UPDATE therapist SET reset_token = ?, reset_token_expiry = ? WHERE therapistID = ?";
                $stmt = $conn->prepare($updateQuery);

                if ($stmt) {
                    $stmt->bind_param("sss", $resetToken, $expiry, $patientId);
                    $stmt->execute();
                    $stmt->close();

                    $resetLink = "https://therapeace-d74d563df28a.herokuapp.com/therapistResetPassword.php?token=" . $resetToken;
                    $subject = "Password Reset Request";
                    $message = "Click the link below to reset your password:\n\n" . $resetLink;
                    $headers = "From: therapeacemanagement@gmail.com";

                    if (mail($email, $subject, $message, $headers)) {
                        $messageDisplay = 'An email with password reset instructions has been sent.';
                    } else {
                        $messageDisplay = 'Failed to send reset email. Please try again later.';
                    }
                } else {
                    $messageDisplay = 'Failed to prepare statement for updating reset token.';
                }
            } else {
                $messageDisplay = 'No patient found with that ID.';
            }
        } else {
            $messageDisplay = 'Failed to prepare statement for patient lookup.';
        }
    }

    // Redirect to avoid form resubmission
    header("Location: therapistForgotPassword.php?message=" . urlencode($messageDisplay));
    exit(); // Prevent further script execution
}

