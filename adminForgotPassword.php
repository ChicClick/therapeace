<?php
include 'admin_forgot_password.php';
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
    <script>
        function disableSubmitButton(form) {
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = "Submitting...";
        }
    </script>
</head>
<body>
    <div class="background-image"></div>
        <div class="background-overlay">
            <div class="forgot-password-box">
                <h2>Forgot Your Password?</h2>
                <p>Enter your Username below, and we'll send you a password reset link.</p>

                <!-- Display the message -->
                <?php echo $messageDisplay; ?>
                
                <form action="admin_forgot_password.php" method="POST" onsubmit="disableSubmitButton(this)">
                    <input type="text" name="username" placeholder="Enter your Username" required>
                    <button type="submit">Submit</button>
                </form>

                <a href="adminlogin.php" class="back-button">Back to Login</a>
            </div>
        </div>
</body>
</html>