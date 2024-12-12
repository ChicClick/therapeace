<?php
include 'db_conn.php'; // Include your database connection file

// Get the therapist ID from the GET request
$therapistID = isset($_GET['therapist_id']) ? $_GET['therapist_id'] : null;

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

    // Fetch therapist's specialization
    $specializationQuery = "
        SELECT specialization FROM therapist WHERE therapistID = ?
    ";
    $stmt = $conn->prepare($specializationQuery);
    $stmt->bind_param("s", $therapistID);
    $stmt->execute();
    $specializationResult = $stmt->get_result();
    $specialization = null;
    if ($specializationResult && $specializationResult->num_rows > 0) {
        $specializationRow = $specializationResult->fetch_assoc();
        $specialization = $specializationRow['specialization'];
    }

    // Generate HTML options
    $options = "";
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $isSelected = $row['serviceID'] == $specialization ? "selected" : "";
            $options .= "<option value='{$row['serviceID']}' $isSelected>{$row['serviceName']}</option>";
        }
    } else {
        $options = "<option value=''>No services available</option>"; // Handle case with no services
    }

    // Return both the options and the therapist's specialization (if needed)
    echo json_encode(['options' => $options, 'specialization' => $specialization]);
}
?>
