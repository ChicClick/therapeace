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
            // Extract date in a readable format for display and pass raw date to JS for query
            $formattedDate = date("F j, Y h:i A", strtotime($row['feedbackdate']));
            $rawDate = $row['feedbackdate'];
            $uniqueID = uniqid(); // Unique ID for each row for toggling

            echo "<tr class='notes-row' onclick=\"fetchFeedback('$rawDate', '$uniqueID')\">";
            echo "<td><img src='images/about 4.jpg' alt='Profile Image'> " . $row['patient_name'] . "</td>";
            echo "<td class='schedule-reschedule'><span>$formattedDate</span></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No appointments found</td></tr>";
    }
?>