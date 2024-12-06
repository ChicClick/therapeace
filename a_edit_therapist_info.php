<?php
include 'db_conn.php';

// Get the therapist ID and updated data from the request
$therapistId = $_POST['therapistID'];
$therapistName = $_POST['therapistName'];
$specialization = $_POST['specialization'];
$phone = $_POST['phone'];
$datehired = $_POST['datehired'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$email = $_POST['email'];
$birthday = $_POST['birthday'];

// Normalize inputs to arrays and cast to integers
$daysAvailable = json_encode(array_map('intval', (array) ($_POST['days_available'] ?? [])));
$timesAvailable = json_encode(array_map('intval', (array) ($_POST['times_available'] ?? [])));
$communication = json_encode(array_map('intval', (array) ($_POST['communication'] ?? [])));
$flexibility = json_encode(array_map('intval', (array) ($_POST['flexibility'] ?? [])));

// SQL query to update therapist information based on therapist ID
$sql = "
    UPDATE therapist
    SET 
        therapistName = ?,
        specialization = ?,
        phone = ?,
        datehired = ?,
        gender = ?,
        address = ?,
        email = ?,
        birthday = ?,
        days_available = ?,
        times_available = ?,
        communication = ?,
        flexibility = ?
    WHERE therapistID = ?
";

// Prepare and bind
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit;
}
$stmt->bind_param(
    "sssssssssssss",
    $therapistName,
    $specialization,
    $phone,
    $datehired,
    $gender,
    $address,
    $email,
    $birthday,
    $daysAvailable,
    $timesAvailable,
    $communication,
    $flexibility,
    $therapistId
);

if ($stmt->execute()) {
    header("Location: admindashboard.php?active=staff-section");
    exit;
} else {
    echo json_encode(['error' => 'Failed to update therapist information']);
}

$stmt->close();
$conn->close();
?>
