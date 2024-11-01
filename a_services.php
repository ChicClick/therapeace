<?php
include 'db_conn.php';

// SQL query to fetch service details including 'about' and 'price' fields
$sql = "
    SELECT 
        services.serviceID, 
        services.serviceName AS service_name,
        services.availability AS service_availability, 
        services.description AS service_description,
        services.about AS service_about,
        services.price AS service_price  
    FROM services
"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='service-row' 
            data-service-name='" . htmlspecialchars($row['service_name'], ENT_QUOTES) . "' 
            data-service-availability='" . htmlspecialchars($row['service_availability'], ENT_QUOTES) . "' 
            data-service-description='" . htmlspecialchars($row['service_description'], ENT_QUOTES) . "' 
            data-service-about='" . htmlspecialchars($row['service_about'], ENT_QUOTES) . "' 
            data-service-price='" . htmlspecialchars($row['service_price'], ENT_QUOTES) . "'>";  // Add price data attribute
        
        // Removed the <img> tag here
        echo "<td>" . htmlspecialchars($row['service_name'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['service_availability'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['service_description'], ENT_QUOTES) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No services found</td></tr>";
}

// Close the database connection
$conn->close();
?>
