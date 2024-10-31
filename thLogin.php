<?php
session_start();
require_once 'db_conn.php'; // Assuming you have a separate file for database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $therapistID = $_POST['therapist_number'];
    $dateHired = $_POST['hire_date'];
    $password = $_POST['password'];

    // Query to check if the therapist exists in the database
    $sql = "SELECT * FROM therapist WHERE therapistID = ? AND dateHired = ? LIMIT 1";
    
    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('ss', $therapistID, $dateHired);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if therapist is found
        if ($result->num_rows === 1) {
            $therapist = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $therapist['password_hash'])) {
                // Password matches, login success
                $_SESSION['therapist_id'] = $therapist['therapistID'];
                $_SESSION['therapist_name'] = $therapist['therapistName']; // Store therapist name in session
                header("Location: therapist-dashboard.php"); // Redirect to dashboard
                exit;
            } else {
                // Invalid password
                $_SESSION['error_message'] = "Invalid password.";
            }
        } else {
            // Therapist not found
            $_SESSION['error_message'] = "Invalid therapist number or hire date.";
        }
        
        $stmt->close();
    } else {
        // Error with the query
        $_SESSION['error_message'] = "Database query error.";
    }
}
header("Location: therapistLogin.php"); // Redirect back to the login form if there's an error
exit;
?>
