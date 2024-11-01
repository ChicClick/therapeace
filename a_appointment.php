<?php

include 'db_conn.php';

$sql = "
    SELECT 
        patient.patientName AS patient_name, 
        therapist.therapistName AS therapist_name, 
        appointment.status AS status,
        services.serviceName AS service_name, 
        appointment.schedule 
    FROM appointment
    JOIN patient ON appointment.patientID = patient.patientID
    JOIN therapist ON appointment.therapistID = therapist.therapistID
    JOIN services ON appointment.serviceID = services.serviceID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image'> " . htmlspecialchars($row['patient_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['therapist_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['service_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>"; // Added status output
        echo "<td class='schedule-reschedule'><span>" . date("Y-m-d h:i A", strtotime($row['schedule'])) . "</span></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No appointments found</td></tr>"; // Adjusted colspan
}
