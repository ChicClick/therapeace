<?php
// a_addstaff.php

include 'db_conn.php';

// Get form data
$staffName = $_POST['staffName'];
$position = $_POST['position'];
$phoneNumber = $_POST['phoneNumber'];
$address = $_POST['address'];
$gender = $_POST['gender'];
$datehired = $_POST['datehired'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO staff (staffName, position, phoneNumber, address, gender, dateHired) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $staffName, $position, $phoneNumber, $address, $gender, $datehired);

// Execute the statement
if ($stmt->execute()) {
    echo "New staff member added successfully.";
    header("Location: admindashboard.php?active=staff-section");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
