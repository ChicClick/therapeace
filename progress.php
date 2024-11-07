<?php
include 'db_conn.php';

// Assuming the therapist's ID is stored in session
$therapist_id = $_SESSION['therapist_id'];

// SQL query to join patient, therapist, and reports tables, and filter by therapistID
$sql = "
    SELECT 
        reports.reportID,
        patient.patientID, 
        patient.patientName AS patient_name, 
        therapist.therapistName AS therapist_name, 
        patient.image AS image,
        reports.status AS guest_status
    FROM reports
    JOIN therapist ON reports.therapistID = therapist.therapistID
    JOIN patient ON reports.patientID = patient.patientID
    WHERE reports.therapistID = ?"; // Filter reports by therapistID

// Prepare statement to avoid SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $therapist_id); // Bind therapist ID as integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Use reportID as the unique ID in the onclick event
        $reportID = htmlspecialchars($row['reportID'], ENT_QUOTES);
        echo "<tr class='progress-row' onclick=\"fetchProgress('$reportID')\">";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image' class='profile-img'> " . htmlspecialchars($row['patient_name'], ENT_QUOTES) . "</td>";
        echo "<td data-parent-name='" . htmlspecialchars($row['therapist_name'], ENT_QUOTES) . "'>" . htmlspecialchars($row['therapist_name'], ENT_QUOTES) . "</td>";
        
        // Status handling
        $status = $row['guest_status'];
        if ($status === "pending") {
            $status_text = "Pending";
            $status_style = "background-color: #FDBC10; padding: 2px; border-radius: 5px;"; 
        } elseif ($status === "verified") {
            $status_text = "Verified";
            $status_style = "background-color: #4FD1C5; padding: 2px; border-radius: 5px;";
        } else {
            $status_text = "Unknown"; 
            $status_style = ""; 
        }

        echo "<td><span style='$status_style'>" . htmlspecialchars($status_text) . "</span></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No profiles found</td></tr>";
}

$conn->close();
?>
