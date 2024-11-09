<?php
include 'db_conn.php';

$patientID = isset($_GET['patientID']) ? $_GET['patientID'] : null;

if ($patientID) {
    $query = "SELECT schedule FROM appointment WHERE patientID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $datetime = new DateTime($row['schedule']);
        $schedules[] = [
            'date' => $datetime->format('Y-m-d'),
            'time' => $datetime->format('H:i')
        ];
    }
    echo json_encode($schedules);
}
?>
