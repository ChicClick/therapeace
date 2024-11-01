<?php
require 'db_conn.php';

// Fetch services
$sql = "SELECT serviceID, serviceName FROM services";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output checkboxes
    while ($row = $result->fetch_assoc()) {
        $serviceID = $row['serviceID'];
        $serviceName = $row['serviceName'];
        echo '<label>';
        echo '<input type="checkbox" name="services[]" value="' . $serviceID . '"> ' . htmlspecialchars($serviceName);
        echo '</label><br>';
    }
} else {
    echo "No services found.";
}

$conn->close();
?>
