<?php
require 'db_conn.php';

$patientId = $_GET['id'];

$sql = "SELECT * FROM patient WHERE patientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $birthday = !empty($row['birthday']) ? (new DateTime($row['birthday']))->format('Y-m-d') : '';

    $response = [
        'patientID' => $row['patientID'],
        'patientName' => $row['patientName'],
        'birthday' => $birthday,
        'address' => $row['address'],
        'phone' => $row['phone'],
        'email' => $row['email'],
        'gender' => $row['gender'],
        'parentID' => $row['parentID'],
        'relationship' => $row['relationship'],
        'status' => $row['status'],
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Patient not found']);
}

