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
        $stmt = $conn->prepare("INSERT INTO therapist (therapistID, specialization, therapistName, availability, email, phone, address, birthday, gender, datehired, password_hash, days_available, times_available, communication, flexibility) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        
        // Bind form data to SQL query parameters
        $stmt->bind_param(
            "sssssssssssssss",
            $therapistID, 
            $_POST['specialization'],
            $_POST['therapistName'], 
            $_POST['availability'], 
            $_POST['email'], 
            $_POST['phone'], 
            $_POST['address'], 
            $_POST['birthday'], 
            $_POST['gender'], 
            $_POST['datehired'], 
            $hashed_password, 
            $_POST['days_available'], 
            $_POST['times_available'],
            $_POST['communication'],
            $_POST['flexibility']
        );

        // Hash the password
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            // Execute the SQL query
            $stmt->execute();
            echo "New therapist added successfully.";
        } catch (mysqli_sql_exception $e) {
            // Check if the error is a duplicate email
            if ($e->getCode() == 1062) { // 1062 is the SQL error code for duplicate entry
                echo "Error: The email address already exists.";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }        

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
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Welcome Email</title>
                </head>
                <body style='font-family: Arial, sans-serif; background-color: #FFF4CE; margin: 0; padding: 0;'>
                    <div style='width: 100%; max-width: 600px; margin: auto; background-color: #FFFFFF; border: 1px solid #FBC22A; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
                        <div style='background-color: #432705; color: #FFF4CE; text-align: center; padding: 20px; font-size: 24px; font-weight: bold;'>
                            Welcome to TheraPeace
                        </div>
                        <div style='padding: 20px; color: #432705; line-height: 1.6;'>
                            Dear <strong style='color: #D57201;'>{$_POST['therapistName']}</strong>,<br><br>

                            Thank you for joining us as a therapist at TheraPeace.<br><br>

                            Your registration was successful. Here are your registration details:<br><br>

                            <strong style='color: #D57201;'>Therapist ID:</strong> {$therapistID}<br>
                            <strong style='color: #D57201;'>Date Hired:</strong> {$_POST['datehired']}<br><br>

                            <b style='color: #FDBC10;'>Password:</b> {$_POST['password']}<br><br>

                            Please <a href='#' style='display: inline-block; margin-top: 20px; background-color: #D57201; color: #FFF4CE; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-size: 16px;'>log in</a> to set your password.<br><br>

                            Best regards,<br>
                            The TheraPeace Team
                        </div>
                        <div style='background-color: #FBC22A; color: #432705; text-align: center; padding: 10px; font-size: 14px;'>
                            &copy; 2024 TheraPeace. All rights reserved.
                        </div>
                    </div>
                </body>
                </html>
            ";
            $mail->send();
            echo " A confirmation email has been sent.";
        } catch (Exception $e) {
            echo " However, the email could not be sent: {$mail->ErrorInfo}";
        }

        echo "<br /><b>Please check your email for details</b>"; // Link back to landing page
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>
