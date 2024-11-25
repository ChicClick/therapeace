<?php
include 'db_conn.php';
include 'config.php';

$therapistID = $_SESSION['therapist_id'];

$query = "SELECT patientID, patientName 
          FROM patient 
          WHERE therapistID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $therapistID);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = [
            'patientID' => $row['patientID'],
            'patientName' => $row['patientName']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($patients);

$stmt->close();
$conn->close();
?>
