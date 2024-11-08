<?php
include 'config.php'; 
include 'db_conn.php'; 

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$messageDisplay = ''; // Initialize a variable for the message to be shown on the front end

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_POST['patientID'] ?? '';

    if (empty($patientId)) {
        $messageDisplay = 'Patient ID is required.';
    } else {
        $query = "SELECT email FROM patient WHERE patientID = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $patientId);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            $stmt->close();

            if ($email) {
                $resetToken = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $updateQuery = "UPDATE patient SET reset_token = ?, reset_token_expiry = ? WHERE patientID = ?";
                $stmt = $conn->prepare($updateQuery);

                if ($stmt) {
                    $stmt->bind_param("sss", $resetToken, $expiry, $patientId);
                    $stmt->execute();
                    $stmt->close();

                    // Create the reset link
                    $resetLink = "https://therapeace-d74d563df28a.herokuapp.com/patientResetPassword.php?token=" . $resetToken;

                    // Create a PHPMailer instance
                    $mail = new PHPMailer(true);

                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'therapeacemanagement@gmail.com';
                        $mail->Password = 'your-gmail-app-password'; // Use your Gmail app-specific password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 465;

                        // Recipients
                        $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace');
                        $mail->addAddress($email); // The patient's email

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Request';
                        $mail->Body = "Click the link below to reset your password:<br><a href='" . $resetLink . "'>Reset Password</a>";

                        // Send the email
                        $mail->send();
                        $messageDisplay = 'An email with password reset instructions has been sent.';
                    } catch (Exception $e) {
                        $messageDisplay = 'Failed to send reset email. Mailer Error: ' . $mail->ErrorInfo;
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
    header("Location: patientForgotPassword.php?message=" . urlencode($messageDisplay));
    exit(); // Prevent further script execution
}
