<?php
include 'config.php';
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_POST['patientID'];
    $inputPassword = $_POST['password'];

    // Debug: Check if data is retrieved
    if (empty($patientId) || empty($inputPassword)) {
        die('Patient ID or Password not provided.');
    }

    // Fetch the hashed password from the database
    $query = "SELECT password_hash FROM patient WHERE patientID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $patientId);
    $stmt->execute();
    $stmt->bind_result($storedHash);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if ($storedHash && password_verify($inputPassword, $storedHash)) {
        // After verifying the password, fetch the patient's name
        $nameQuery = "SELECT patientName FROM patient WHERE patientID = ?";
        $stmt = $conn->prepare($nameQuery);
        $stmt->bind_param("s", $patientId);
        $stmt->execute();
        $stmt->bind_result($patientName);
        $stmt->fetch();
        $stmt->close();

        // Store patient ID and name in session
        $_SESSION['patientID'] = $patientId;
        $_SESSION['patientName'] = $patientName; // Store the patient's name

        // Redirect to homepage
        header("Location: loading.php");
        exit;
    } else {
        // If login fails, set error message and redirect back to login page
        $_SESSION['error_message'] = "Invalid Patient ID or password.";
        header("Location: patientLogin.php");
        exit;
    }
}

// Close connection
$conn->close();
?>
