<?php
include 'config.php';
// Include database connection
include 'db_conn.php'; // Ensure you have a file for database connection

// Check if the patient is logged in
if (!isset($_SESSION['patientID'])) {
    header("Location: patientLogin.php"); // Redirect to login if not logged in
    exit();
}

// Fetch the patient ID from the session
$patientID = $_SESSION['patientID'];

// Prepare the SQL query to fetch patient details with additional information
$sql = "
    SELECT 
        p.patientName, 
        p.email, 
        p.phone, 
        p.address, 
        p.birthday, 
        p.gender, 
        p.schedule,
        p.relationship,
        parent.parentName, 
        therapist.therapistName,
        s.serviceName
    FROM 
        patient AS p
    LEFT JOIN 
        parent ON p.parentID = parent.parentID
    LEFT JOIN 
        therapist ON p.therapistID = therapist.therapistID
    LEFT JOIN 
        services AS s ON p.serviceID = s.serviceID  -- Join with services table based on serviceID
    WHERE 
        p.patientID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID); // "s" indicates the type of the parameter (string)
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch patient data
    $patient = $result->fetch_assoc();
    $patientName = $patient['patientName'];
    $patientEmail = $patient['email'];
    $patientPhone = $patient['phone'];
    $patientAddress = $patient['address'];
    $patientBirthday = $patient['birthday'];
    $patientGender = $patient['gender'];
    $patientSchedule = $patient['schedule'];
    $patientRelationship = $patient['relationship'];
    $parentName = $patient['parentName'];
    $therapistName = $patient['therapistName'];
    $serviceName = $patient['serviceName'];
} else {
    // Handle case where no patient data is found
    echo "No patient data found.";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
