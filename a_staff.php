<?php
include 'db_conn.php';

// SQL query to fetch staff details
$sql = "
    SELECT 
        staff.staffID, 
        staff.staffName AS staff_name,
        staff.position AS staff_position, 
        staff.datehired AS staff_datehired
    FROM staff
"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-staff-id='" . $row['staffID'] . "' data-staff-name='" . htmlspecialchars($row['staff_name'], ENT_QUOTES) . "'>";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image' class='profile-img'> " . htmlspecialchars($row['staff_name'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['staff_position'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['staff_datehired'], ENT_QUOTES) . "</td>";
        echo "<td><button class='edit-staff-profile' id='edit-staff-profile'>Edit Staff</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No staff profiles found</td></tr>"; // Adjusted colspan for 3 columns
}

// Close the database connection
$conn->close();
?>
