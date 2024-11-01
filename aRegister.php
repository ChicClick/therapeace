<?php
// Database connection details
$host = "localhost:3307";  
$username = "root";  
$password = "";  
$dbname = "therapeacedb";

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO admin (adminID, firstname, lastname, username, phoneNumber, address, birthday, gender, password_hash) 
                                VALUES (NULL, :firstName, :lastName, :username, :phone, :address, :birthday, :gender, :password_hash)");

        // Bind form data to SQL query parameters
        $stmt->bindParam(':firstName', $_POST['firstName']);
        $stmt->bindParam(':lastName', $_POST['lastName']);
        $stmt->bindParam(':username', $_POST['email']);  // Assuming email as username
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':birthday', $_POST['birthday']);
        $stmt->bindParam(':gender', $_POST['gender']);

        // Hash the password and bind it
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $hashed_password);

        // Execute the SQL query
        $stmt->execute();

        // Output success message
        echo "Registration successful!";
        echo "<br><a href='adminlogin.php'>Proceed to Login</a>"; // Link to login page
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
