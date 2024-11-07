<?php
include 'db_conn.php';

// Check if POST data is set
if (isset($_POST['serviceName'], $_POST['availability'], $_POST['description'], $_POST['about'], $_POST['price'])) {
    $serviceName = $conn->real_escape_string($_POST['serviceName']);
    $availability = $conn->real_escape_string($_POST['availability']);
    $description = $conn->real_escape_string($_POST['description']);
    $about = $conn->real_escape_string($_POST['about']);
    $price = $conn->real_escape_string($_POST['price']);

    // Insert data into the service table
    $sql = "INSERT INTO services (serviceName, availability, description, about, price) VALUES ('$serviceName', '$availability', '$description', '$about', '$price')";

    if ($conn->query($sql) === TRUE) {
        echo "Service added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Please fill all fields.";
}

// Close the connection
$conn->close();
?>
