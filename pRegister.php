<?php
include 'db_conn.php';
try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO patient (patientID, patientName, phone, email, address, birthday, gender, parentID, relationship, serviceID, status, image, password_hash) 
                                VALUES (:patientID, :patientName, :phone, :email, :address, :birthday, :gender, :parentID, :relationship, :serviceID, :status, :image, :password_hash)");

        // Bind form data to SQL query parameters
        $stmt->bindParam(':patientID', $_POST['patientID']);
        $stmt->bindParam(':patientName', $_POST['patientName']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':address', $_POST['address']);
        $stmt->bindParam(':birthday', $_POST['birthday']);
        $stmt->bindParam(':gender', $_POST['gender']);
        $stmt->bindParam(':parentID', $_POST['parentID']);
        $stmt->bindParam(':relationship', $_POST['relationship']);
        $stmt->bindParam(':serviceID', $_POST['serviceID']);
        $stmt->bindParam(':status', $_POST['status']);

        // Handle image file upload
        if (is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
            $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB);
        } else {
            $stmt->bindValue(':image', null, PDO::PARAM_NULL);
        }

        // Hash the password and bind it
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $hashed_password);

        // Execute the SQL query
        $stmt->execute();

        // Send email with credentials
        $to = $_POST['email'];
        $subject = "Your Registration Details";
        $message = "Dear " . $_POST['patientName'] . ",\n\nThank you for registering.\n\nHere are your credentials:\n\n";
        $message .= "Username: " . $_POST['patientID'] . "\n";
        $message .= "Password: " . $_POST['password'] . " (Please remember to change your password after your first login)\n\n";
        $message .= "Best regards,\nTheraPeace Team";

        // Headers
        $headers = "From: no-reply@therapeace.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";

        // Send the email
        if (mail($to, $subject, $message, $headers)) {
            echo "Patient registration successful! A confirmation email has been sent.";
        } else {
            echo "Patient registration successful! However, the email could not be sent.";
        }

        echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>"; // Link back to landing page
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
