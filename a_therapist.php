<?php
include 'db_conn.php';

// SQL query to fetch therapist details
$sql = "
    SELECT 
        therapist.therapistID, 
        therapist.therapistName AS therapist_name,
        therapist.specialization AS therapist_specialization, 
        therapist.datehired AS therapist_datehired
    FROM therapist
"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-therapist-id='" . $row['therapistID'] . "' data-therapist-name='" . htmlspecialchars($row['therapist_name'], ENT_QUOTES) . "'>";
        echo "<td><img src='images/about 4.jpg' alt='Profile Image' class='profile-img'> " . htmlspecialchars($row['therapist_name'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['therapist_specialization'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['therapist_datehired'], ENT_QUOTES) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No therapist profiles found</td></tr>"; // Adjusted colspan for 3 columns
}

// Close the database connection
$conn->close();
?>
