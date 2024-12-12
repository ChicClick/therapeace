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
    $schedule = isset($_POST['schedule']) ? json_decode($_POST['schedule'], true) : null;

    if (!is_array($schedule)) {
        echo json_encode(["error" => "Invalid schedule format. Must be an array."]);
        exit();
    }

    if (!$patientID || !$parentID || !$therapistID || !$serviceID || !$schedule) {
        echo json_encode(["error" => "All fields are required."]);
        exit();
    }

    function checkExistence($table, $column, $value)
    {
        global $conn;
        $allowedTables = ['parent', 'therapist', 'services'];
        $allowedColumns = ['parentID', 'therapistID', 'serviceID'];

        if (!in_array($table, $allowedTables) || !in_array($column, $allowedColumns)) {
            return false;
        }

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

    $sql = "INSERT INTO appointment (patientID, parentID, therapistID, serviceID, schedule) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Failed to prepare SQL statement."]);
        exit();
    }

    foreach ($schedule as $scheduleTime) {
        $stmt->bind_param("sssss", $patientID, $parentID, $therapistID, $serviceID, $scheduleTime);

        if (!$stmt->execute()) {
            echo json_encode(["error" => "Failed to execute SQL statement for schedule $scheduleTime."]);
            exit();
        }
    }

    echo json_encode(["success" => "Appointments inserted successfully."]);

    $stmt->close();
    $conn->close();
}
