<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'db_conn.php'; // This should already initialize the connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind the SQL statement for inserting parent data
    $stmt = $conn->prepare("INSERT INTO parent (parentName, contactno) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['parentName'], $_POST['contactno']); // "ss" specifies the data types (string)

    // Execute the SQL query
    if ($stmt->execute()) {
        // Output success message
        echo "Parent information saved successfully!";
        echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>"; // Link back to landing page
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
