<?php
include 'db_conn.php';

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO parent (parentName, contactno) VALUES (:parentName, :contactno)");

        // Bind form data to SQL query parameters
        $stmt->bindParam(':parentName', $_POST['parentName']);
        $stmt->bindParam(':contactno', $_POST['contactno']);

        // Execute the SQL query
        $stmt->execute();

        // Output success message
        echo "Parent information saved successfully!";
        echo "<br><a href='registerlanding.html'>Back to Registration Landing</a>"; // Link back to landing page
        exit(); // Exit to prevent further script execution
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
