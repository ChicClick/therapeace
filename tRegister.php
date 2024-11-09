<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'db_conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate a unique therapistID
    $result = $conn->query("SELECT MAX(therapistID) AS maxID FROM therapist");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $lastID = $row['maxID'];
    $therapistID = "T" . str_pad(substr($lastID, 1) + 1, 3, '0', STR_PAD_LEFT); // Generates the ID as 'T001', 'T002', etc.

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO therapist (therapistID, specialization, therapistName, availability, email, phone, address, birthday, gender, datehired, password_hash) 
                                VALUES (:therapistID, :specialization, :therapistName, :availability, :email, :phone, :address, :birthday, :gender, :datehired, :password_hash)");

        // Bind form data to SQL query parameters
        $stmt->bindParam(':therapistID', $therapistID);
        $stmt->bindParam(':specialization', $_POST['specialization']);
        $stmt->bindParam(':therapistName', $_POST['therapistName']);
        $stmt->bindParam(':availability', $_POST['availability']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':birthday', $_POST['birthday']);
        $stmt->bindParam(':gender', $_POST['gender']);
        $stmt->bindParam(':datehired', $_POST['date-hired']);

        // Hash the password and bind it
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $hashed_password);

        // Execute the SQL query
        $stmt->execute();

        echo "Therapist registration successful!";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings for PHPMailer
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'therapeacemanagement@gmail.com';
            $mail->Password = 'ovzp bnem esqd nqyn'; // Replace with app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Email recipients and content
            $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace Team');
            $mail->addAddress($_POST['email']);
            $mail->isHTML(true);
            $mail->Subject = "Your Registration Details";
            $mail->Body = "
                Dear {$_POST['therapistName']},<br><br>
                Thank you for joining us as a therapist at TheraPeace.<br><br>
                Here are your credentials:<br>
                <b>Therapist ID:</b> {$therapistID}<br>
                <b>Email:</b> {$_POST['email']}<br>
                <b>Password:</b> {$_POST['password']} (Please remember to change your password after your first login)<br><br>
                Best regards,<br>TheraPeace Team
            ";

            $mail->send();
            echo " A confirmation email has been sent.";
        } catch (Exception $e) {
            echo " However, the email could not be sent: {$mail->ErrorInfo}";
        }

        echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>"; // Link back to landing page
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
