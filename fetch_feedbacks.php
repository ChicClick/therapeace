<?php
include 'config.php'; // Make sure this includes your database connection

// Query to get feedbacks with a rating between 3 and 5 and consent set to true
$sql = "SELECT f.feedback_text, f.rating, f.created_at, p.parentName 
        FROM feedbacks f
        JOIN parent p ON f.parentID = p.parentID
        WHERE f.rating BETWEEN 3 AND 5 AND f.consent = 1
        ORDER BY f.created_at DESC";

$result = mysqli_query($conn, $sql);

// Check if there are any results
if (mysqli_num_rows($result) > 0) {
    // Loop through results and display feedback
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='feedback-item'>";
        echo "<p>\"" . htmlspecialchars($row['feedback_text']) . "\"</p>";
        echo "<h4>" . htmlspecialchars($row['parentName']) . " - Rating: " . $row['rating'] . "</h4>";
        echo "<small>Submitted on: " . $row['created_at'] . "</small>";
        echo "</div>";
    }
} else {
    // Display message when no feedback is available
    echo "<p>No feedback available at the moment.</p>";
}
?>
