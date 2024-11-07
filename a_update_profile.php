<?php
// Include the database connection
include('db_conn.php');

// Start the session to access the admin's ID
session_start();
$admin_id = $_SESSION['adminID']; // Admin ID from session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];
    $birthYear = $_POST['birthYear'];
    $birthMonth = $_POST['birthMonth'];
    $birthDay = $_POST['birthDay'];

    // Format the birth date
    $birthDate = $birthYear . '-' . $birthMonth . '-' . $birthDay;

    // Check if the username is already used by another admin (excluding the current admin)
    $emailCheckQuery = "SELECT adminID FROM admin WHERE username = ? AND adminID != ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("si", $username, $admin_id);
    $stmt->execute();
    $stmt->store_result();

    // If the email already exists for another admin
    if ($stmt->num_rows > 0) {
        echo "This email is already in use by another admin. Please choose a different email.";
        $stmt->close();
    } else {
        // Prepare the UPDATE query for admin profile
        $query = "UPDATE admin SET 
                    firstname = ?, 
                    lastname = ?, 
                    username = ?, 
                    address = ?, 
                    phoneNumber = ?, 
                    birthday = ? 
                  WHERE adminID = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($query)) {
            // Bind the parameters to the query
            $stmt->bind_param("ssssssi", $firstName, $lastName, $username, $address, $phoneNumber, $birthDate, $admin_id);

            // Execute the statement
            if ($stmt->execute()) {
                // Success message
                echo "Profile updated successfully!";
            } else {
                // Error message if update fails
                echo "Error updating profile: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            // If the query preparation fails
            echo "Error preparing the query: " . $conn->error;
        }
    }
}
?>
