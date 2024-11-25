<?php
require 'db_conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$conn) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    $patientID = $_POST['patientID'] ?? null;
    $parentID = $_POST['parentID'] ?? null;
    $therapistID = $_POST['therapistID'] ?? null;
    $serviceID = $_POST['serviceID'] ?? null;
    $schedule = $_POST['schedule'] ?? null;

    // Check if all required fields are filled
    if (!$patientID || !$parentID || !$therapistID || !$serviceID || !$schedule) {
        echo json_encode(["error" => "All fields are required."]);
        exit();
    }

    function checkExistence($table, $column, $value) {
        global $conn;
        $stmt = $conn->prepare("SELECT 1 FROM $table WHERE $column = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    if (!checkExistence('parent', 'parentID', $parentID)) {
        echo json_encode(["error" => "ParentID does not exist."]);
        exit();
    }
    if (!checkExistence('therapist', 'therapistID', $therapistID)) {
        echo json_encode(["error" => "TherapistID does not exist."]);
        exit();
    }
    if (!checkExistence('services', 'serviceID', $serviceID)) {
        echo json_encode(["error" => "ServiceID does not exist."]);
        exit();
    }

    // Insert appointment into database
    $sql = "INSERT INTO appointment (patientID, parentID, therapistID, serviceID, schedule) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Failed to prepare SQL statement."]);
        exit();
    }

    $stmt->bind_param("sssss", $patientID, $parentID, $therapistID, $serviceID, $schedule);
    if ($stmt->execute()) {
        echo json_encode(["success" => "Appointment scheduled successfully."]);
    } else {
        echo json_encode(["error" => "Error saving appointment."]);
    }

    // Clean up
    $stmt->close();
    $conn->close();
}
?>
