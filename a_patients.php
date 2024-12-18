<?php
include 'db_conn.php';

// SQL query to join the patient and parent tables and fetch the names and image
$sql = "
    SELECT 
        patient.patientID, 
        patient.patientName AS patient_name, 
        parent.parentName AS parent_name,
        services.serviceName AS service_name,
        patient.image AS image,
        patient.status AS patient_status
    FROM patient
    JOIN parent ON patient.parentID = parent.parentID
    JOIN services ON patient.serviceID = services.serviceID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-patient-id='" . $row['patientID'] . "' data-patient-name='" . htmlspecialchars($row['patient_name'], ENT_QUOTES) . "'>";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image' class='profile-img'> " . htmlspecialchars($row['patient_name'], ENT_QUOTES) . "</td>";
        echo "<td data-parent-name='" . htmlspecialchars($row['parent_name'], ENT_QUOTES) . "'>" . htmlspecialchars($row['parent_name'], ENT_QUOTES) . "</td>";
        echo "<td>" . $row['service_name'] . "</td>";
        echo "<td>" . htmlspecialchars($row['patient_status'], ENT_QUOTES) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No profiles found</td></tr>";
}

// Close the database connection
$conn->close();
?>