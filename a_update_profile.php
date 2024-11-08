<?php
// Start the session at the very beginning
session_start();

// Check if the admin is logged in by checking if 'username' is set in the session
if (!isset($_SESSION['username'])) {
    echo "Admin not logged in!";
    exit();
}

// Include the database connection
include('db_conn.php');

// Get the logged-in admin's username from the session
$username = $_SESSION['username'];

// Fetch the admin's ID based on the username
$query = "SELECT adminID FROM admin WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $admin_id = $admin['adminID'];
} else {
    echo "Admin not found!";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure form fields are set
    $firstName = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastName = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phoneNumber = isset($_POST['phoneNumber']) ? trim($_POST['phoneNumber']) : '';
    $birthYear = isset($_POST['birthYear']) ? trim($_POST['birthYear']) : '';
    $birthMonth = isset($_POST['birthMonth']) ? trim($_POST['birthMonth']) : '';
    $birthDay = isset($_POST['birthDay']) ? trim($_POST['birthDay']) : '';

    // Format the birth date
    $birthDate = "$birthYear-$birthMonth-$birthDay";

    // Check if the username is already used by another admin (excluding the current admin)
    $emailCheckQuery = "SELECT adminID FROM admin WHERE username = ? AND adminID != ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("si", $username, $admin_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Username is already taken
        echo "This email is already in use by another admin. Please choose a different email.";
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

        $stmt->close();

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
