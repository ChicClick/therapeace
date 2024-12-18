<?php
include 'config.php'; 
include 'db_conn.php'; 

// for sending email
require_once 'generic_mailer.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$messageDisplay = ''; // Initialize a variable for the message to be shown on the front end

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';

    if (empty($username)) {
        $messageDisplay = 'Username is required.';
    } else {
        $query = "SELECT username FROM admin WHERE username = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            $stmt->close();

            if ($email) {
                $resetToken = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $updateQuery = "UPDATE admin SET reset_token = ?, reset_token_expiry = ? WHERE username = ?";
                $stmt = $conn->prepare($updateQuery);

                if ($stmt) {
                    $stmt->bind_param("sss", $resetToken, $expiry, $username);
                    $stmt->execute();
                    $stmt->close();

                    // updated the domain
                    $resetLink = "  /adminResetPassword.php?token=" . $resetToken;
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
    header("Location: adminForgotPassword.php?message=" . urlencode($messageDisplay));
    exit();
}
