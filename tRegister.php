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

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Generate a unique therapistID
        $result = $conn->query("SELECT MAX(therapistID) AS maxID FROM therapist");
        $row = $result->fetch_assoc();
        $lastID = $row['maxID'];
        $therapistID = "T" . str_pad(substr($lastID, 1) + 1, 3, '0', STR_PAD_LEFT); // Generates the ID as 'T001', 'T002', etc.

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO therapist (therapistID, specialization, therapistName, availability, email, phone, address, birthday, gender, datehired, password_hash, days_available, times_available) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind form data to SQL query parameters
        $stmt->bind_param("sssssssssssss", $therapistID, $_POST['specialization'], $_POST['therapistName'], $_POST['availability'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['birthday'], $_POST['gender'], $_POST['datehired'], $hashed_password, $_POST['days_available'], $_POST['times_available']);

        // Hash the password
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

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
                Your registration was successful. Here are your registration details:<br><br>
                <strong>Therapist ID:</strong> {$therapistID}<br>
                <strong>Date Hired:</strong> {$_POST['date-hired']}<br><br>
                 <b>Password:</b> {$_POST['password']}<br><br>
                Please log in to set your password.<br><br>
                Best regards,<br>TheraPeace Team
            ";
            $mail->send();
            echo " A confirmation email has been sent.";
        } catch (Exception $e) {
            echo " However, the email could not be sent: {$mail->ErrorInfo}";
        }

        echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>"; // Link back to landing page
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>
