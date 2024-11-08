<?php
include 'config.php'; // Database connection settings
include 'db_conn.php'; // Database connection script

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$messageDisplay = ''; // Initialize a variable to hold the message for redirection

// Check if there's a token in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and hasn't expired
    $query = "SELECT patientID, reset_token_expiry, email FROM patient WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($patientId, $expiry, $email);
    $stmt->fetch();
    $stmt->close();

    if ($patientId) {
        $currentTime = date('Y-m-d H:i:s');

        // Check if the token has expired
        if ($currentTime > $expiry) {
            $messageDisplay = 'This reset link has expired.';
            header("Location: patientResetPassword.php?message=" . urlencode($messageDisplay));
            exit();
        } else {
            // Handle form submission to reset the password
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $newPassword = $_POST['password'];

                // Validate password length
                if (strlen($newPassword) < 6) {
                    $messageDisplay = 'Password must be at least 6 characters long.';
                    // Redirect back to the same page with the token and error message
                    header("Location: patientResetPassword.php?token=" . urlencode($token) . "&message=" . urlencode($messageDisplay));
                    exit();
                } else {
                    // Update the password in the database
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateQuery = "UPDATE patient SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE patientID = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("ss", $hashedPassword, $patientId);
                    $stmt->execute();
                    $stmt->close();

                    // Send confirmation email using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'therapeacemanagement@gmail.com';
                        $mail->Password = 'ovzp bnem esqd nqyn'; // Use your Gmail app-specific password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;

                        // Recipients
                        $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace');
                        $mail->addAddress($email); // The patient's email

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Confirmation';
                        $mail->Body = "Your password has been successfully reset. You can now log in using your new password.";

                        // Send the email
                        $mail->send();
                    } catch (Exception $e) {
                        $messageDisplay = 'Password reset successful, but failed to send confirmation email. Mailer Error: ' . $mail->ErrorInfo;
                        header("Location: patientResetPassword.php?message=" . urlencode($messageDisplay));
                        exit();
                    }

                    // Password reset successful
                    $_SESSION['message'] = 'Your password has been reset successfully. Please log in with your new password.';
                    header("Location: patientLogin.php?message=" . urlencode($_SESSION['message']));
                    exit();
                }
            }
        }
    } else {
        $messageDisplay = 'Invalid token.';
        header("Location: patientResetPassword.php?message=" . urlencode($messageDisplay));
        exit();
    }
} else {
    $messageDisplay = 'No reset token found.';
    header("Location: patientResetPassword.php?message=" . urlencode($messageDisplay));
    exit();
}
?>
