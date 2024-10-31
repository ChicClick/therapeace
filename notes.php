<?php

include 'db_conn.php';

$sql = "
    SELECT 
        patient.patientName AS patient_name,  
        sessionfeedbacknotes.feedbackdate 
    FROM sessionfeedbacknotes
    JOIN patient ON sessionfeedbacknotes.patientID = patient.patientID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        // Generate a unique ID or use an index for notes identification
        echo "<tr class='notes-row' data-notes-id='1'>"; // Adjust data-notes-id as needed
        echo "<td><img src='images/about 4.jpg' alt='Profile Image'> " . $row['patient_name'] . "</td>";
        echo "<td class='schedule-reschedule'><span>" . date("F j, Y h:i A", strtotime($row['feedbackdate'])) . "</span></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='2'>No appointments found</td></tr>";
}
?>
