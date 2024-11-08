<?php
include 'config.php'; // Database connection settings
include 'db_conn.php'; // Database connection script

$messageDisplay = ''; // Initialize a variable to hold the message for redirection

// Check if there's a token in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and hasn't expired
    $query = "SELECT patientID, reset_token_expiry FROM patient WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($patientId, $expiry);
    $stmt->fetch();
    $stmt->close();

    if ($patientId) {
        $currentTime = date('Y-m-d H:i:s');

        // Check if the token has expired
        if ($currentTime > $expiry) {
            $messageDisplay = 'This reset link has expired.';
            header("Location: therapistResetPassword.php?message=" . urlencode($messageDisplay));
            exit();
        } else {
            // Handle form submission to reset the password
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $newPassword = $_POST['password'];

                // Validate password length
                if (strlen($newPassword) < 6) {
                    $messageDisplay = 'Password must be at least 6 characters long.';
                    // Redirect back to the same page with the token and error message
                    header("Location: therapistResetPassword.php?token=" . urlencode($token) . "&message=" . urlencode($messageDisplay));
                    exit();
                } else {
                    // Update the password in the database
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateQuery = "UPDATE patient SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE patientID = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("ss", $hashedPassword, $patientId);
                    $stmt->execute();
                    $stmt->close();

                    // Password reset successful
                    $_SESSION['message'] = 'Your password has been reset successfully. Please log in with your new password.';
                    header("Location: therapistLogin.php?message=" . urlencode($_SESSION['message']));
                    exit();
                }
            }
        }
    } else {
        $messageDisplay = 'Invalid token.';
        header("Location: therapistResetPassword.php?message=" . urlencode($messageDisplay));
        exit();
    }
} else {
    $messageDisplay = 'No reset token found.';
    header("Location: therapistResetPassword.php?message=" . urlencode($messageDisplay));
    exit();
}
?>
