<?php
// Include the database connection file
include 'db_conn.php';

// Create connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO parent (parentName, contactno) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['parentName'], $_POST['contactno']); // "ss" specifies the data types (string)

    // Execute the SQL query
    if ($stmt->execute()) {
        // Output success message
        echo "Parent information saved successfully!";
        echo "<br><a href='registerlanding.html'>Back to Registration Landing</a>"; // Link back to landing page
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
