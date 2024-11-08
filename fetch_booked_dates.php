<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include 'db_conn.php'; // Ensure the path is correct

// Query to get booked schedules
$sql = "SELECT DATE_FORMAT(schedule, '%Y-%m-%d') AS date, 
               TIME_FORMAT(schedule, '%H:%i') AS time 
        FROM appointment"; // Adjust 'appointment' to your table name if needed

$result = $conn->query($sql);

// Initialize an array to store booked dates
$bookedDates = [];

// Fetch the results and group by date
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = $row['date'];
            $time = $row['time'];

            // Group times by date
            if (!isset($bookedDates[$date])) {
                $bookedDates[$date] = [];
            }
            $bookedDates[$date][] = $time;
        }
    } else {
        echo json_encode([]); // Return an empty array if no results found
        exit; // Stop execution
    }
} else {
    die("Query failed: " . $conn->error);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($bookedDates); // Change here to return bookedDates

// Close the database connection
$conn->close();
?>
