<?php
include 'config.php'; 
include 'db_conn.php'; 

require_once 'generic_mailer.php';

date_default_timezone_set('Asia/Singapore');

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
                    $stmt->bind_param("sss", $resetToken, $expiry, $therapistId);
                    $stmt->execute();
                    $stmt->close();

                    $resetLink = "https://therapeace-d74d563df28a.herokuapp.com/therapistResetPassword.php?token=" . $resetToken;
                    $subject = "Password Reset Request";

                    try {
                        $mailer = new Mailer();
                        $toEmail = $email;
                        $subject = 'Password Reset Request';
                        $body = '
                        <div style="font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; background-color: #FFF4CE; border: 1px solid #FDBC10; border-radius: 8px; max-width: 600px; margin: auto; color: #432705;">
                        <h2 style="color: #D57201; text-align: center;">Password Reset Request</h2>
                        <p>A request was received to reset the password for your <strong>Therapeace</strong> account.</p>
                        <p>Click the link below to reset your password:</p>
                        <p style="text-align: center;">
                            <a href="' . $resetLink . '" style="display: inline-block; padding: 12px 20px; background-color: #FBC22A; color: #432705; text-decoration: none; font-weight: bold; border-radius: 5px; border: 1px solid #FDBC10;">Reset Password</a>
                        </p>
                        <p style="margin-top: 20px;">If you did not request a password reset, please ignore this email.</p>
                        <p style="text-align: center; font-size: 12px; margin-top: 20px; color: #D57201;">
                            &copy; 2024 Therapeace, All rights reserved.
                        </p>
                        </div>';

                        $mailer->sendEmail($toEmail, $subject, $body);
                        $messageDisplay = 'An email with password reset instructions has been sent.';
                    } catch (Exception $e) {
                        $messageDisplay = 'Failed to send reset email. Mailer Error: ' . $e->getMessage();
                    }
                } else {
                    $messageDisplay = 'Failed to prepare statement for updating reset token.';
                }
            } else {
                $messageDisplay = 'No therapist found with that ID.';
            }
        } else {
            $messageDisplay = 'Failed to prepare statement for therapist lookup.';
        }
    }

    // Redirect to avoid form resubmission
    header("Location: therapistForgotPassword.php?message=" . urlencode($messageDisplay));
    exit(); // Prevent further script execution
}

