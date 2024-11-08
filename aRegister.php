<?php
// Include the database connection file
include('config.php');

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if POST data is being received
    if (!isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['birthday'], $_POST['gender'], $_POST['password'])) {
        echo "<script>alert('Form data is incomplete!');</script>";
        exit();
    }

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

    // Execute the SQL query and check if it was successful
    if ($stmt->execute()) {
        echo "<script>alert('User successfully registered in database.');</script>";

        // Get the email address from the form
        $email = $_POST['email'];

        // Prepare the email message
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
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'therapeacemanagement@gmail.com'; 
            $mail->Password = 'ovzp bnem esqd nqyn'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace Management');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            // Debugging output
            $mail->SMTPDebug = 2;  // Enable SMTP debug output

            // Send the email
            $mail->send();
            echo "<script>alert('Registration successful! A confirmation email has been sent.');</script>";

            // Redirect to login page after successful registration
            header("Location: adminlogin.php");
            exit();
        } catch (Exception $e) {
            // If PHPMailer fails to send email
            echo "<script>alert('Error sending confirmation email: {$mail->ErrorInfo}');</script>";
        }
    } else {
        // If the SQL insert fails
        echo "<script>alert('Error: Could not register user in the database.');</script>";
    }
}

// Close the connection
$conn = null;
?>
