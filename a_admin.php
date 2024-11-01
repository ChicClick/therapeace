<?php
include 'db_conn.php';

// SQL query to fetch admin details
$sql = "
    SELECT 
        admin.adminID, 
        CONCAT(admin.firstname, ' ', admin.lastname) AS admin_name, 
        admin.phoneNumber AS admin_contactnumber,
        admin.birthday AS admin_birthday
    FROM admin
"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-admin-id='" . $row['adminID'] . "' data-admin-name='" . htmlspecialchars($row['admin_name'], ENT_QUOTES) . "'>";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image' class='profile-img'> " . htmlspecialchars($row['admin_name'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['admin_contactnumber'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['admin_birthday'], ENT_QUOTES) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No admin profiles found</td></tr>"; // Adjusted colspan for 3 columns
}

// Close the database connection
$conn->close();
?>
