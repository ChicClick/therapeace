<?php
// Include database connection
include 'db_conn.php';

// Fetch appointments
$sql = "
    SELECT 
        patient.patientName AS patient_name, 
        parent.parentName AS parent_name, 
        service.serviceName AS service_name, 
        appointment.schedule 
    FROM appointment
    JOIN patient ON appointment.patientID = patient.patientID
    JOIN parent ON appointment.parentID = parent.parentID
    JOIN service ON appointment.serviceID = service.serviceID
";

$result = $conn->query($sql);

$scheduledDates = []; // Array to hold already scheduled dates
if ($result->num_rows > 0) {
    // Output data for each row and store the scheduled dates
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image'> " . $row['patient_name'] . "</td>";
        echo "<td>" . $row['parent_name'] . "</td>";
        echo "<td>" . $row['service_name'] . "</td>";
        echo "<td class='schedule-reschedule'>
                <span>" . date("F j, Y h:i A", strtotime($row['schedule'])) . "</span> 
                <a href='#' class='reschedule-link' data-id='" . $row['schedule'] . "'>Reschedule</a>
              </td>";
        echo "</tr>";
        // Add the scheduled date to the array
        $scheduledDates[] = date("Y-m-d", strtotime($row['schedule']));
    }
} else {
    echo "<tr><td colspan='4'>No appointments found</td></tr>";
}

// Pass scheduled dates to JavaScript in JSON format
echo "<script>var bookedDates = " . json_encode($scheduledDates) . ";</script>";
?>
