<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include('db_conn.php');

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

    // Prepare the SQL statement with positional placeholders
    $stmt = $conn->prepare("INSERT INTO admin (adminID, firstname, lastname, username, phoneNumber, address, birthday, gender, password_hash) 
                            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Hash the password
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Bind form data to SQL query parameters (order matters)
    $stmt->bind_param('sssssssss', $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['birthday'], $_POST['gender'], $hashed_password);

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
            if ($mail->send()) {
                echo "<script>alert('Registration successful! A confirmation email has been sent.');</script>";
                // Redirect to login page after successful registration
                header("Location: adminlogin.php");
                exit();
            } else {
                echo "<script>alert('Error sending confirmation email: {$mail->ErrorInfo}');</script>";
            }
        } catch (Exception $e) {
            // If PHPMailer fails to send email
            echo "<script>alert('Error sending confirmation email: {$mail->ErrorInfo}');</script>";
        }
    } else {
        // If the SQL insert fails
        echo "<script>alert('Error: Could not register user in the database.');</script>";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
