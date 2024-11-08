<?php
include('db_conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt->bindParam(':password_hash', $hashed_password);

    if ($stmt->execute()) {
        $email = $_POST['email'];
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

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'therapeacemanagement@gmail.com';
            $mail->Password = 'ovzp bnem esqd nqyn'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace Management');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->SMTPDebug = 2;  // Enable debug output

            $mail->send();
            header("Location: adminlogin.php");
            exit(); 
        } catch (Exception $e) {
            echo "Error sending confirmation email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: Could not register user in the database.";
    }

    $conn = null;
}
?>