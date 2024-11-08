<?php
// Include database connection
include 'db_conn.php';

// Assuming the therapist's ID is stored in session
$therapist_id = $_SESSION['therapist_id'];

// Fetch appointments for the logged-in therapist
$sql = "
    SELECT 
        patient.patientName AS patient_name, 
        parent.parentName AS parent_name, 
        services.serviceName AS service_name, 
        appointment.schedule,
        appointment.appointmentID
    FROM appointment
    JOIN patient ON appointment.patientID = patient.patientID
    JOIN parent ON appointment.parentID = parent.parentID
    JOIN services ON appointment.serviceID = services.serviceID
    WHERE appointment.therapistID = ?"; // Add therapist filter here

// Prepare statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $therapist_id); // "i" stands for integer parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add each scheduled date to an array for use in JavaScript
        $scheduledDates[] = $row['schedule']; // Push the schedule to the array

        echo "<tr>";
        echo "<td><input type='hidden' class='appointment-id' value='" . htmlspecialchars($row['appointmentID']) . "'>" . htmlspecialchars($row['appointmentID']) . "</td>";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image'> " . htmlspecialchars($row['patient_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['parent_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['service_name']) . "</td>";
        echo "<td class='schedule-reschedule'>
                <span>" . date("F j, Y h:i A", strtotime($row['schedule'])) . "</span> 
                <a href='#' class='reschedule-link' data-id='" . htmlspecialchars($row['schedule']) . "'>Reschedule</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No appointments found</td></tr>";
}
?>
