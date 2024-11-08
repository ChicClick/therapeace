<?php
include 'db_conn.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffID = $_POST['staffID'];
    $staffName = $_POST['staffName'];
    $position = $_POST['position'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $dateHired = $_POST['datehired'];

    // Update query
    $sql = "
        UPDATE staff 
        SET 
            staffName = ?, 
            position = ?, 
            phoneNumber = ?, 
            address = ?, 
            gender = ?, 
            datehired = ?
        WHERE 
            staffID = ?
    ";

    // Prepare and bind parameters
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssssi', $staffName, $position, $phoneNumber, $address, $gender, $dateHired, $staffID);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Staff profile updated successfully.';
        } else {
            $response['message'] = 'Failed to update staff profile.';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Failed to prepare SQL statement.';
    }
}

$conn->close();
echo json_encode($response);
