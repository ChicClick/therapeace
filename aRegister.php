<?php
include 'db_conn.php'; // Ensure $conn is properly initialized

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

        // Get the email address from the form
        $email = $_POST['email'];

        // Prepare the email message
        $subject = "Registration Successful";
        $message = "
        <html>
        <head>
        <title>Registration Confirmation</title>
        </head>
        <body>
        <p>Dear {$_POST['firstName']} {$_POST['lastName']},</p>
        <p>Your registration has been successfully completed. Below are your credentials:</p>
        <p><strong>Username (Email):</strong> {$email}</p>
        <p><strong>Password:</strong> {$_POST['password']}</p>
        <p>Thank you for registering!</p>
        </body>
        </html>
        ";

        // Set email headers for HTML format
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
        $headers .= "From: therapeacemanagement@gmail.com" . "\r\n";  // Replace with your sender email

        // Send the email
        if(mail($email, $subject, $message, $headers)) {
            echo "Registration successful! A confirmation email has been sent.";
        } else {
            echo "Error sending confirmation email.";
        }

        echo "<br><a href='adminlogin.php'>Proceed to Login</a>"; // Link to login page
        exit(); // Exit to prevent further script execution
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
