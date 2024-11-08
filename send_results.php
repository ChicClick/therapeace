<?php
include 'db_conn.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_GET['guest_id'])) {
    $guest_id = (int)$_GET['guest_id'];

    // Fetch guest details, including matchTherapy
    $sql = "SELECT GuestName, email, matchTherapy FROM guest WHERE GuestID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $guest_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $guest = $result->fetch_assoc();
        $guest_name = htmlspecialchars($guest['GuestName']);
        $guest_email = htmlspecialchars($guest['email']);
        $matchTherapy = htmlspecialchars($guest['matchTherapy']); // Therapy type they are matched with

        // Set interview date to 1 week from today and time to 8:00 AM
        $interview_date = new DateTime();
        $interview_date->modify('+1 week');
        $interview_date->setTime(8, 0);

        // Store the interview date in MySQL format (YYYY-MM-DD)
        $formatted_date = $interview_date->format('Y-m-d');
        $interview_time = $interview_date->format('H:i:s');
        $interview_datetime = $formatted_date . ' ' . $interview_time;

        // Update the schedule column in the guest table
        $update_sql = "UPDATE guest SET schedule = ? WHERE GuestID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $interview_datetime, $guest_id);
        $update_stmt->execute();

        // Format the interview date for display
        $human_readable_date = $interview_date->format('l, F j, Y');
        $interview_time_display = $interview_date->format('h:i A');
        
        // Compose the email
        $message = "Hello $guest_name,<br><br>"
           . "We are pleased to inform you that you have qualified for our therapy program at TheraBee!<br><br>"
           . "Based on your assessment, we believe you would benefit from the following therapy: <b>$matchTherapy</b>.<br><br>"
           . "To help us get to know you better and discuss the next steps, we invite you to an in-person interview. Below are the details:<br><br>"
           . "Date: <b>$human_readable_date</b><br>"
           . "Time: <b>$interview_time_display</b><br>"
           . "Location: <b>Unit 2-M, EC Valle Commercial Complex, Quezon Ave, Angono, Rizal, Philippines</b><br><br>"
           . "Please feel free to reach out if you have any questions. We’re excited to support you on this journey and are here to help every step of the way.<br><br>"
           . "Kindly reply to this email to confirm your attendance.<br><br>"
           . "Warm regards,<br>"
           . "The TheraBee Child Development and Learning Center Team";

        // Use PHPMailer to send the email
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
            $mail->setFrom('therapeacemanagement@gmail.com', 'TheraBee Center');
            $mail->addAddress($guest_email, $guest_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'TheraBee Center Assessment Results';
            $mail->Body    = $message;

            // Send the email
            $mail->send();
            echo "<script>
                alert('Results sent successfully to $guest_email.');
                window.location.href = 'therapist-dashboard.php';
            </script>";
        } catch (Exception $e) {
            echo "<script>
                alert('Failed to send results. Mailer Error: {$mail->ErrorInfo}');
                window.location.href = 'therapist-dashboard.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Guest not found.');
            window.location.href = 'therapist-dashboard.php';
        </script>";
    }
} else {
    echo "<script>
        alert('No guest ID provided.');
        window.location.href = 'therapist-dashboard.php';
    </script>";
}
?>
