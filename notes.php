<?php
    include 'db_conn.php';

    // Get therapist ID from session
    $therapist_id = $_SESSION['therapist_id']; 

    // SQL query to join sessionfeedbacknotes and patient tables, filtered by therapistID
    $sql = "
        SELECT 
            patient.patientName AS patient_name,  
            sessionfeedbacknotes.feedbackdate 
        FROM sessionfeedbacknotes
        JOIN patient ON sessionfeedbacknotes.patientID = patient.patientID
        WHERE patient.therapistID = ?"; // Filter feedback notes by therapistID

    // Prepare the query to avoid SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $therapist_id); // Bind therapist ID as integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            // Extract date in a readable format for display and pass raw date to JS for query
            $formattedDate = date("F j, Y", strtotime($row['feedbackdate']));
            $rawDate = $row['feedbackdate'];
            $uniqueID = uniqid(); // Unique ID for each row for toggling

            echo "<tr class='notes-row' onclick=\"fetchFeedback('$rawDate', '$uniqueID')\">";
            echo "<td><img src='images/about 4.jpg' alt='Profile Image'> " . htmlspecialchars($row['patient_name'], ENT_QUOTES) . "</td>";
            echo "<td class='schedule-reschedule'><span>$formattedDate</span></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No feedback found</td></tr>";
    }

    $conn->close();
?>
