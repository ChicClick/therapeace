require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('GMAIL_USER') ?: $_ENV['GMAIL_USER']; 
    $mail->Password = getenv('GMAIL_PASS') ?: $_ENV['GMAIL_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 465;

    // Recipients
    $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace');
    $mail->addAddress($email); // $email should be defined elsewhere in your code

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "Click the link below to reset your password:<br><a href=https://therapeace-d74d563df28a.herokuapp.com/patientResetPassword.php</a>";

    // Send the email
    $mail->send();
    $messageDisplay = 'An email with password reset instructions has been sent.';
} catch (Exception $e) {
    $messageDisplay = 'Failed to send reset email. Mailer Error: ' . $mail->ErrorInfo;
}
