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
                                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if the prepare statement was successful
        if (!$stmt) {
            die("Error in SQL preparation: " . $conn->error);
        }

        // Handle image file upload
        $imageData = null;
        if (is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
        }

        // Hash the password
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Bind form data to SQL query parameters
        $stmt->bind_param(
            "sssssssssb",  // 12 parameters: s for strings, b for binary
            $_POST['patientName'], 
            $_POST['phone'], 
            $_POST['email'], 
            $_POST['address'], 
            $_POST['birthday'], 
            $_POST['gender'], 
            $_POST['parentID'], 
            $_POST['relationship'], 
            $_POST['serviceID'], 
            $_POST['status'], 
            $imageData, 
            $hashed_password
        );

        // Execute the SQL query
        if ($stmt->execute()) {
            echo "Patient registration successful!";

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings for PHPMailer
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'therapeacemanagement@gmail.com';
                $mail->Password = 'your_app_specific_password'; // Replace with your app-specific password
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

            echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>";
        } else {
            echo "Error executing SQL: " . $stmt->error;
        }

        $stmt->close();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close the connection
$conn->close();
?>
