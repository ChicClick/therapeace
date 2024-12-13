<?php
include 'db_conn.php'; // Include your database connection file

// Get the patient ID from the GET request
$patientID = isset($_GET['patientID']) ? $_GET['patientID'] : null;
$therapistID = isset($_GET['therapistID']) ? $_GET['therapistID'] : null;

if ($patientID) {
    // Query to fetch services linked to the selected patient's appointments
    $query = "
        SELECT DISTINCT
    s.serviceID,
    s.serviceName,
    t.specialization
    FROM
        services s
    INNER JOIN
        appointment a ON s.serviceID = a.serviceID
    INNER JOIN
        therapist t ON a.therapistID = t.therapistID
    WHERE
        a.patientID = ?
        AND s.serviceID = t.specialization
    ";

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate HTML options
    $options = "";
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='{$row['serviceID']}'>{$row['serviceName']}</option>
                         <input type='hidden' name='serviceID' value='{$row['serviceID']}' />
            ";
        }
    } else {
        $options = "<option value=''>No services available</option>";
        
    }
    echo $options; // Output the options
}
