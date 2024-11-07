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

    // Update query with placeholders
    $sql = "UPDATE services SET availability=?, description=?, About=?, price=? WHERE serviceName=?";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute
    $stmt->bind_param("sssss", $availability, $description, $about, $price, $serviceName);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
