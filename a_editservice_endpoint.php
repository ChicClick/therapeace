<?php
include 'db_conn.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $serviceName = $_POST['service-name'];
    $availability = $_POST['availability'];
    $description = $_POST['description'];
    $about = $_POST['about'];
    $price = $_POST['price'];
    $id = intval($_POST['id']);  // Fix here

    // Update query with placeholders
    $sql = "UPDATE services SET serviceName=?, availability=?, description=?, About=?, price=? WHERE serviceID=?";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute
    $stmt->bind_param("sssssi", $serviceName, $availability, $description, $about, $price, $id);  // Fix bind_param type

    if ($stmt->execute()) {
        // Redirect before echoing JSON
        header("Location: admindashboard.php?active=services-section");
        exit;  // Make sure the script stops executing
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
