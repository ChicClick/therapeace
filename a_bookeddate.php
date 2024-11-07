<?php

include 'db_conn.php';

$sql = "
    SELECT DISTINCT DATE(appointment.schedule) AS appointment_date
    FROM appointment
";

$result = $conn->query($sql);

$bookedDates = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ensure date is formatted as 'YYYY-MM-DD'
        $bookedDates[] = $row['appointment_date'];
    }
}

// Return the booked dates as a JSON response
echo json_encode($bookedDates);

// Close the database connection
$conn->close();
?>
