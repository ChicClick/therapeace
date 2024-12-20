<?php
include 'config.php'; 
include 'db_conn.php';

require_once 'generic_mailer.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$messageDisplay = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_POST['patientID'] ?? '';

    if (empty($patientId)) {
        $messageDisplay = 'Patient ID is required.';
    } else {
        $query = "SELECT email, phone FROM patient WHERE patientID = ?";
        $stmt = $conn->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param("s", $patientId);
            $stmt->execute();
    
            $stmt->bind_result($email, $phone);
            $stmt->fetch();
            $stmt->close();
    
            if ($email && $phone) {
                $resetToken = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $updateQuery = "UPDATE patient SET reset_token = ?, reset_token_expiry = ? WHERE patientID = ?";
                $stmt = $conn->prepare($updateQuery);
    
                if ($stmt) {
                    $stmt->bind_param("sss", $resetToken, $expiry, $patientId);
                    $stmt->execute();
                    $stmt->close();
    
                    $resetLink = "  /patientResetPassword.php?token=" . $resetToken;

                    try {
                        $mailer = new Mailer();
                        $toEmail = $email;
                        $subject = 'Password Reset Request';
                        $body = 'A request was received to reset the password for your Therapeace account.<br><br>Click the link below to reset your password:<br><a href=' . $resetLink . '>Reset Password</a>';
    
                        $mailer->sendEmail($toEmail, $subject, $body);
                        $messageDisplay = 'An email with password reset instructions has been sent.';
                    } catch (Exception $e) {
                        $messageDisplay = 'Failed to send reset email. Mailer Error: ' . $e->getMessage();
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

    echo $messageDisplay;
    header("Location: patientForgotPassword.php?message=" . urlencode($messageDisplay));
    exit();
}