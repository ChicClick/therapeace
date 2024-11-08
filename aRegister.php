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
    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO admin (adminID, firstname, lastname, username, phoneNumber, address, birthday, gender, password_hash) 
                            VALUES (NULL, :firstName, :lastName, :username, :phone, :address, :birthday, :gender, :password_hash)");

    // Bind form data to SQL query parameters
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

    // Prepare the email message (this is your original message)
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
    <p><strong>Password:</strong> {$_POST['password']}</p>
    <p>Thank you for registering!</p>
    </body>
    </html>
    ";

    // Send email using PHPMailer
    $mail = new PHPMailer(true);

   try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'therapeacemanagement@gmail.com'; // Your Gmail address
        $mail->Password   = 'ovzp bnem esqd nqyn'; // Your Gmail password or App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace Management');
        $mail->addAddress($email); // Add the user's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Send the email
        $mail->send();
        echo "Registration successful! A confirmation email has been sent.";
    } catch (Exception $e) {
        echo "Error sending confirmation email: {$mail->ErrorInfo}";
    }

    // Output the link to proceed to login
    echo "<br><a href='adminlogin.php'>Proceed to Login</a>";
    exit(); // Exit to prevent further script execution
}

// Close the connection
$conn = null;
?>
