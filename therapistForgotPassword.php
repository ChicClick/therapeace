<?php
include 'therapist_forgot_password.php';
$messageDisplay = '';

if (isset($_GET['message'])) {
    $messageDisplay = '<div class="message">' . htmlspecialchars(urldecode($_GET['message'])) . '</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="patientPasswordStyles.css">
</head>
<body>
    <div class="background-image"></div>
        <div class="background-overlay">
            <div class="forgot-password-box">
                <h2>Forgot Your Password?</h2>
                <p>Enter your Therapist ID below, and we'll send you a password reset link.</p>

                <!-- Display the message -->
                <?php echo $messageDisplay; ?>
                
                <form action="therapist_forgot_password.php" method="POST">
                    <input type="text" name="therapistID" placeholder="Enter your Therapist ID" required>
                    <button type="submit">Submit</button>
                </form>

                <a href="therapistLogin.php" class="back-button">Back to Login</a>
            </div>
        </div>
</body>
</html>
