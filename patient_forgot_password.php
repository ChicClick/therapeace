require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
$mail->Username = $_ENV['GMAIL_USER'] ?? getenv('GMAIL_USER');
$mail->Password = $_ENV['GMAIL_PASS'] ?? getenv('GMAIL_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $mail->Body    = "Click the link below to reset your password:<br><a href='" . $resetLink . "'>" . $resetLink . "</a>";
    
    $mail->send();
    $messageDisplay = 'An email with password reset instructions has been sent.';
} catch (Exception $e) {
    $messageDisplay = 'Failed to send reset email. Mailer Error: ' . $mail->ErrorInfo;
}
