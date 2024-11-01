<?php
include 'db_conn.php';

// Prepare and bind
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetching the posted data
    $serviceName = $_POST['service-name']; // Ensure this matches the input name
    $availability = $_POST['availability'];
    $description = $_POST['description'];
    $about = $_POST['about']; // Ensure this matches the input name
    $price = $_POST['price'];

    // Update the service in the database
    $sql = "UPDATE services SET availability=?, description=?, About=?, price=? WHERE serviceName=?";
    $stmt = $conn->prepare($sql);
    
    // Use 's' for strings, and the last parameter should match the correct data type (string in this case)
    $stmt->bind_param("ssssd", $availability, $description, $about, $price, $serviceName);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
