<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO admin (adminID, firstname, lastname, username, phoneNumber, address, birthday, gender, password_hash) 
                                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Error in SQL preparation: " . $conn->error);
        }

        // Bind form data to the query parameters
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssssssss", $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['birthday'], $_POST['gender'], $hashed_password);

        // Execute the SQL query
        if (!$stmt->execute()) {
            die("Error executing SQL: " . $stmt->error);
        }

        echo "Registration successful!";

        // Prepare to send the email
        $email = $_POST['email'];
        $subject = "Registration Successful";
        $message = "Dear {$_POST['firstName']} {$_POST['lastName']},<br><br>
                    Your registration has been successfully completed. Below are your credentials:<br>
                    Username (Email): {$email}<br>
                    Password: {$_POST['password']}<br><br>
                    Thank you for registering!";

        // Send email using PHPMailer
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

            $mail->send();
            echo "<br>Confirmation email has been sent.";
        } catch (Exception $e) {
            echo "Error sending confirmation email: {$mail->ErrorInfo}";
        }

        echo "<br><a href='adminlogin.php'>Proceed to Login</a>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
$conn->close();
?>
