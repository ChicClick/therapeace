<?php
require 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check connection
    if (!$conn) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get and validate inputs
    $patientID = $_POST['patient-ID'] ?? null;
    $parentID = $_POST['parentID'] ?? null;
    $therapistID = $_POST['therapist'] ?? null;
    $serviceIDs = isset($_POST['services']) ? $_POST['services'] : []; // array of service IDs
    $schedule = $_POST['schedule'] ?? null;

    // Check if all required fields are filled
    if (empty($patientID) || empty($parentID) || empty($therapistID) || empty($serviceIDs) || empty($schedule)) {
        die("All fields are required.");
    }

    // Verify if patientID exists in the patient table
    $stmt = $conn->prepare("SELECT 1 FROM patient WHERE patientID = ?");
    $stmt->bind_param("s", $patientID); // Bind as string
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        die("Error: The selected PatientID does not exist in the patient table.");
    }
    $stmt->close();

    // Verify if parentID exists in the parent table
    $stmt = $conn->prepare("SELECT 1 FROM parent WHERE parentID = ?");
    $stmt->bind_param("s", $parentID); // Bind as string
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        die("Error: The selected ParentID does not exist in the parent table.");
    }
    $stmt->close();

    // Verify if therapistID exists in the therapist table
    $stmt = $conn->prepare("SELECT 1 FROM therapist WHERE therapistID = ?");
    $stmt->bind_param("s", $therapistID); // Bind as string
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        die("Error: The selected TherapistID does not exist in the therapist table.");
    }
    $stmt->close();

    // Verify each serviceID exists in the services table
    foreach ($serviceIDs as $serviceID) {
        $stmt = $conn->prepare("SELECT 1 FROM services WHERE serviceID = ?");
        $stmt->bind_param("s", $serviceID); // Bind as string
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            die("Error: The selected ServiceID {$serviceID} does not exist in the services table.");
        }
        $stmt->close();
    }

    // Prepare SQL statement for appointment insertion
    $sql = "INSERT INTO appointment (patientID, parentID, therapistID, serviceID, schedule) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind and execute for each service ID
    foreach ($serviceIDs as $serviceID) {
        $stmt->bind_param("sssss", $patientID, $parentID, $therapistID, $serviceID, $schedule); // Bind as strings

        if (!$stmt->execute()) {
            // Provide more detailed error message
            echo "Error saving appointment for service ID {$serviceID}: " . $stmt->error . "<br>";
            $stmt->close();
            $conn->close();
            exit(); // Exit on error to avoid further issues
        }
    }

    // Clean up and close connections
    $stmt->close();
    $conn->close();

    echo "Appointments saved successfully.";
    echo "<br><a href='admindashboard.php'>Proceed</a>"; 
}
?>
