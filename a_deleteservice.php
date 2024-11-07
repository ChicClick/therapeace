<?php
// Database connection
include 'db_conn.php';

// Check if serviceID is set
if (isset($_POST['serviceID'])) {
    $serviceID = $conn->real_escape_string($_POST['serviceID']);

    // Delete query
    $sql = "DELETE FROM services WHERE serviceID = '$serviceID'";

    if ($conn->query($sql) === TRUE) {
        echo "Service deleted successfully!";
    } else {
        echo "Error deleting service: " . $conn->error;
    }
} else {
    echo "Service ID not provided.";
}

$conn->close();
?>
