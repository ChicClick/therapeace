<?php
include 'db_conn.php';
include 'config.php';

if (isset($_POST['appointmentID'])) {
    $appointmentID = $conn->real_escape_string($_POST['appointmentID']);

    $sql = "UPDATE appointment SET status = 'completed' WHERE appointmentID = '$appointmentID'";

    if ($conn->query($sql) === TRUE) {
        header("Location: admindashboard.php?active=appointments-section");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Please fill all fields.";
}

$conn->close();
?>
