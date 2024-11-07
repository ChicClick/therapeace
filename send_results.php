<?php
include 'db_conn.php';

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
        $interview_date = new DateTime(); // Current time (just the date)
        $interview_date->modify('+1 week'); // Add 1 week
        $interview_date->setTime(8, 0); // Explicitly set the time to 08:00 AM

        // Store the interview date in the format MySQL expects (YYYY-MM-DD)
        $formatted_date = $interview_date->format('Y-m-d');
        $interview_time = $interview_date->format('H:i:s'); // 08:00:00
        $interview_datetime = $formatted_date . ' ' . $interview_time;

        // Update the schedule column in the guest table with the interview datetime
        $update_sql = "UPDATE guest SET schedule = ? WHERE GuestID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $interview_datetime, $guest_id);
        $update_stmt->execute();

        // Format the interview date for display in human-readable format
        $human_readable_date = $interview_date->format('l, F j, Y'); // Example: "Thursday, November 14, 2024"
        $interview_time_display = $interview_date->format('h:i A'); // Example: "08:00 AM"
        
        // Compose the email with the specific therapy type included
        $message = "Hello $guest_name,<br><br>"
                 . "We are pleased to inform you that you have qualified for our therapy program.<br><br>"
                 . "Based on your assessment, we recommend the following therapy: <b>$matchTherapy</b>.<br><br>"
                 . "If you wish to proceed, we are inviting you to an in-person interview. Here are the details:<br><br>"
                 . "Date: <b>$human_readable_date</b><br>"
                 . "Time: <b>$interview_time_display</b><br>"
                 . "Location: <b>Unit 2-M EC Valle Commercial Complex Quezon Ave Angono, Rizal, Angono, Philippines</b><br><br>"
                 . "Please let us know if you have questions. We look forward to supporting you in this journey.<br><br>"
                 . "Kindly reply to this email for confirmation.<br><br>"
                 . "Best regards,<br>TheraBee Child Development and Learning Center";

        $subject = "TheraBee Center Assesment Results";
        $headers = "From: therapeacemanagement@gmail.com" . "\r\n" .
                   "Content-Type: text/html; charset=UTF-8"; // Set headers for HTML content

        // Send the email
        if (mail($guest_email, $subject, $message, $headers)) {
            // Success: show an alert and redirect
            echo "<script>
                alert('Results sent successfully to $guest_email.');
                window.location.href = 'therapist-dashboard.php';
            </script>";
        } else {
            // Failure: show an error alert
            echo "<script>
                alert('Failed to send results.');
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
