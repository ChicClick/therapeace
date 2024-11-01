<?php
require 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['patient'])) {
    $patientId = $_POST['patient'];

    $sql = "SELECT patientName, phone, parentID FROM patient WHERE patientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patientInfo = $result->fetch_assoc();
        echo json_encode($patientInfo);
    } else {
        echo json_encode(['error' => 'No patient found']);
    }
    $stmt->close();
}

$conn->close();
?>
