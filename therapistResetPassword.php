<?php
include 'therapist_reset_password.php';
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
            <div class="container">
                <div class="forgot-password-box">
                    <h2>Create New Password</h2>
                    <p>Enter your new password below with at least 6 characters.</p>

                    <!-- Display the message -->
                    <?php echo $messageDisplay; ?>

                    <!-- Password Reset Form -->
                    <form action="therapist_reset_password.php?token=<?php echo $_GET['token']; ?>" method="POST">
                        <input type="password" name="password" placeholder="Enter your new password" required>
                        <button type="submit">Submit New Password</button>
                    </form>

                    <a href="therapistLogin.php" class="back-button">Back to Login</a>

                </div>
            </div>
        </div>
</body>
</html>
