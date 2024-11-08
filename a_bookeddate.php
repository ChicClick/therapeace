<?php

include 'db_conn.php';

// $times = ["09:00", "10:00", "11:00", "12:00", "01:00", "02:00", "03:00", "04:00", "05:00", "06:00"];
$times = ["09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00"];

$sql = "
    SELECT DATE(appointment.schedule) AS appointment_date, 
           TIME(appointment.schedule) AS appointment_time 
    FROM appointment
";

$result = $conn->query($sql);

$datesWithBookings = [];

// Group booked times by date
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date = $row['appointment_date'];
        $time = substr($row['appointment_time'], 0, 5); // Only include HH:MM
        
        if (!isset($datesWithBookings[$date])) {
            $datesWithBookings[$date] = [];
        }
        $datesWithBookings[$date][] = $time;
    }
}

// Check if each date is fully booked
$fullyBookedDates = [];

foreach ($datesWithBookings as $date => $booked_times) {
    // Find the common times between the booked times and the full time slots
    $intersected_times = array_intersect($times, $booked_times);
    
    if (count($intersected_times) === count($times)) {
        $fullyBookedDates[] = $date;
    }
}

// echo json_encode($bookedDates);

// Return the dates that are fully booked as a JSON response
/* echo json_encode([
    "fullyBookedDates" => $fullyBookedDates
]);
*/

echo json_encode($fullyBookedDates);

// Close the database connection
$conn->close();
?>
