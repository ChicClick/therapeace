<?php
include 'db_conn.php'; // Include your database connection file

// Get the patient ID from the GET request
$patientID = isset($_GET['patientID']) ? $_GET['patientID'] : null;

if ($patientID) {
    // Query to fetch services linked to the selected patient's appointments
    $query = "
        SELECT DISTINCT s.serviceID, s.serviceName 
        FROM service s
        INNER JOIN appointment a ON s.serviceID = a.serviceID
        WHERE a.patientID = ?
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
            $options .= "<option value='{$row['serviceID']}'>{$row['serviceName']}</option>";
        }
    } else {
        $options = "<option value=''>No services available</option>"; // Handle case with no services
    }
    echo $options; // Output the options
}
?>
