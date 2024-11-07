<?php
include 'config.php';
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $inputPassword = $_POST['password']; // User-entered password

    // Fetch the stored hash for this username
    $query = "SELECT password_hash FROM admin WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($storedHash);
    $stmt->fetch();
    $stmt->close();

    // Verify the password
    if ($storedHash && password_verify($inputPassword, $storedHash)) {
        // After verifying the password, fetch the patient's name
        $nameQuery = "SELECT firstname FROM admin WHERE username = ?";
        $stmt = $conn->prepare($nameQuery);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($firstname);
        $stmt->fetch();
        $stmt->close();

        // Store patient ID and name in session
        $_SESSION['username'] = $username;
        $_SESSION['firstname'] = $firstname; // Store the patient's name

        // Redirect to homepage
        header("Location: a_loading.php");
        exit;
    } else {
        // If login fails, set error message and redirect back to login page
        $_SESSION['error_message'] = "Invalid username or password.";
        header("Location: adminlogin.php");
        exit;
    }
}

// Close connection
$conn->close();
?>

