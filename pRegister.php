<?php
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
    try {
        // Prepare the SQL statement for inserting patient data
        $stmt = $conn->prepare("INSERT INTO patient (patientID, patientName, phone, email, address, birthday, gender, parentID, relationship, serviceID, status, image, password_hash) 
                                VALUES (:patientID, :patientName, :phone, :email, :address, :birthday, :gender, :parentID, :relationship, :serviceID, :status, :image, :password_hash)");

        // Bind form data to SQL query parameters
        $stmt->bindParam(':patientID', $_POST['patientID']);
        $stmt->bindParam(':patientName', $_POST['patientName']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':birthday', $_POST['birthday']);
        $stmt->bindParam(':gender', $_POST['gender']);
        $stmt->bindParam(':parentID', $_POST['parentID']);
        $stmt->bindParam(':relationship', $_POST['relationship']);
        $stmt->bindParam(':serviceID', $_POST['serviceID']);
        $stmt->bindParam(':status', $_POST['status']);

        // Handle image file upload
        if (is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
            $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB);
        } else {
            $stmt->bindValue(':image', null, PDO::PARAM_NULL);
        }

        // Hash the password and bind it
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $hashed_password);

        // Execute the SQL query
        $stmt->execute();
        echo "Patient registration successful!";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings for PHPMailer
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'therapeacemanagement@gmail.com';
            $mail->Password = 'ovzp bnem esqd nqyn'; // Use your app-specific password here
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Email recipients and content
            $mail->setFrom('therapeacemanagement@gmail.com', 'TheraPeace Team');
            $mail->addAddress($_POST['email']);
            $mail->isHTML(true);
            $mail->Subject = "Your Registration Details";
            $mail->Body = "
                Dear {$_POST['patientName']},<br><br>
                Thank you for registering with TheraPeace.<br><br>
                Here are your credentials:<br>
                <b>Username (Patient ID):</b> {$_POST['patientID']}<br>
                <b>Password:</b> {$_POST['password']}<br><br>
                Please remember to change your password after your first login.<br><br>
                Best regards,<br>TheraPeace Team
            ";

            $mail->send();
            echo " A confirmation email has been sent.";
        } catch (Exception $e) {
            echo " However, the email could not be sent: {$mail->ErrorInfo}";
        }

        echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>"; // Link back to landing page

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close the connection
$conn = null;
?>
