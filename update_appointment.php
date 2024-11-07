<?php
// Include database connection file
include('db_conn.php');

// Get the POST data
$originalDate = isset($_POST['originalDate']) ? $_POST['originalDate'] : '';
$newSchedule = isset($_POST['newSchedule']) ? $_POST['newSchedule'] : '';

// Ensure the data is valid
if (!empty($originalDate) && !empty($newSchedule)) {
    // Ensure that the new schedule is in the correct datetime format
    $originalDate = date('Y-m-d H:i:s', strtotime($originalDate));  // Ensure it's in 'YYYY-MM-DD HH:MM:SS' format
    $newSchedule = date('Y-m-d H:i:s', strtotime($newSchedule));    // Same format for the new schedule

    // Prepare the SQL statement to update the schedule
    $sql = "UPDATE appointment SET schedule = ? WHERE schedule = ?";

    // Prepare and execute the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $newSchedule, $originalDate);

        if ($stmt->execute()) {
            echo "Appointment rescheduled successfully!";
        } else {
            echo "Error updating appointment: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "Invalid data provided.";
}

// Close the connection
$conn->close();
?>
