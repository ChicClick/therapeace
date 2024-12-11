<?php
include 'db_conn.php'; // Include your database connection file

// Get the therapist ID from the GET request
$therapistID = isset($_GET['therapistID']) ? $_GET['therapistID'] : null;

if ($therapistID) {
    // Query to fetch services linked to the therapist's specialization
    $query = "
        SELECT DISTINCT s.serviceID, s.serviceName 
        FROM services s
        INNER JOIN therapist ts ON s.serviceID = ts.specialization
        WHERE ts.therapistID = ?
    ";

    // Prepare and execute the statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $therapistID);
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
