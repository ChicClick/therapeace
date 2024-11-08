<?php
// Include the database connection file
include('db_conn.php');

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO admin (adminID, firstname, lastname, username, phoneNumber, address, birthday, gender, password_hash) 
                                VALUES (NULL, :firstName, :lastName, :username, :phone, :address, :birthday, :gender, :password_hash)");

        $stmt->bindParam(':firstName', $_POST['firstName']);
        $stmt->bindParam(':lastName', $_POST['lastName']);
        $stmt->bindParam(':username', $_POST['email']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':birthday', $_POST['birthday']);
        $stmt->bindParam(':gender', $_POST['gender']);

        // Hash the password and bind it
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $hashed_password);

        // Execute the SQL query
        $stmt->execute();

        // Get the email address from the form
        $email = $_POST['email'];

        // Prepare the email message (do not include the password)
        $subject = "Registration Successful";
        $message = "
        <html>
        <head>
        <title>Registration Confirmation</title>
        </head>
        <body>
        <p>Dear {$_POST['firstName']} {$_POST['lastName']},</p>
        <p>Your registration has been successfully completed. Below are your credentials:</p>
        <p><strong>Username (Email):</strong> {$email}</p>
        <p>Thank you for registering!</p>
        </body>
        </html>
        ";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('GMAIL_USERNAME'); // Store in environment variable
        $mail->Password = getenv('GMAIL_APP_PASSWORD'); // Store in environment variable
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace Management');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        echo "Registration successful! A confirmation email has been sent.";
        echo "<br><a href='adminlogin.php'>Proceed to Login</a>";
    } catch (Exception $e) {
        // Handle error
        echo "Error: {$e->getMessage()}";
    } finally {
        // Close the connection
        $conn = null;
    }
}
?>
