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
        $stmt = $conn->prepare("INSERT INTO therapist (therapistID, specialization, therapistName, availability, email, phone, address, birthday, gender, datehired, password_hash) 
                                VALUES (:therapistID, :specialization, :therapistName, :availability, :email, :phone, :address, :birthday, :gender, :datehired, :password_hash)");

        // Bind form data to SQL query parameters
        $stmt->bindParam(':therapistID', $_POST['therapistID']);
        $stmt->bindParam(':specialization', $_POST['specialization']);
        $stmt->bindParam(':therapistName', $_POST['therapistName']);
        $stmt->bindParam(':availability', $_POST['availability']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':birthday', $_POST['birthday']);
        $stmt->bindParam(':gender', $_POST['gender']);
        $stmt->bindParam(':datehired', $_POST['date-hired']);

        // Hash the password and bind it
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $hashed_password);

        // Execute the SQL query
        $stmt->execute();

        // Send email with credentials
        $to = $_POST['email'];
        $subject = "Your Registration Details";
        $message = "Dear " . $_POST['therapistName'] . ",\n\nThank you for joining us as a therapist.\n\nHere are your credentials and employment details:\n\n";
        $message .= "Therapist ID: " . $_POST['therapistID'] . "\n";
        $message .= "Specialization: " . $_POST['specialization'] . "\n";
        $message .= "Date Hired: " . $_POST['date-hired'] . "\n";
        $message .= "Password: " . $_POST['password'] . " (Please remember to change your password after your first login)\n\n";
        $message .= "Best regards,\nTheraPeace Team";

        // Headers
        $headers = "From: no-reply@therapeace.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";

        // Send the email
        if (mail($to, $subject, $message, $headers)) {
            echo "Therapist registration successful! A confirmation email has been sent.";
        } else {
            echo "Therapist registration successful! However, the email could not be sent.";
        }

        echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>"; // Link back to landing page
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
