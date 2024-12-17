<?php
include 'db_conn.php';

// Define the service icon array
$service_icons = [
    'Speech Therapy' => 'fa-solid fa-comments',
    'Occupational Therapy' => 'fa-solid fa-hands-holding-child',
    'Physical Therapy' => 'fa-solid fa-wheelchair-move',
    'Online Pre-Screening' => 'fa-solid fa-clipboard-list',
];

// Fetch services from the database
$sql = "SELECT * FROM services";
$result = $conn->query($sql);

$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add the icon to each service dynamically
        $row['icon'] = isset($service_icons[$row['serviceName']]) ? $service_icons[$row['serviceName']] : 'fa-solid fa-cogs';
        $services[] = $row;
    }
}

// Close the database connection
$conn->close();
?>
