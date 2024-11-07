<?php
// Include the database connection
include('db_conn.php');

// Start the session to access the therapist's ID and update the session variables
session_start();
$therapist_id = $_SESSION['therapist_id']; // Therapist ID from session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $birthYear = $_POST['birthYear'];
    $birthMonth = $_POST['birthMonth'];
    $birthDay = $_POST['birthDay'];

    // Concatenate first and last name to store in therapistName
    $fullName = $firstName . ' ' . $lastName;
    // Format the birth date
    $birthDate = $birthYear . '-' . $birthMonth . '-' . $birthDay;

    // Check if the email is already used by another therapist (excluding the current therapist)
    $emailCheckQuery = "SELECT therapistID FROM therapist WHERE email = ? AND therapistID != ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("ss", $email, $therapist_id); // Check for email, excluding the current therapist's ID
    $stmt->execute();
    $stmt->store_result();

    // If the email already exists for another therapist
    if ($stmt->num_rows > 0) {
        echo "<script>alert('This email is already in use by another therapist. Please choose a different email.');</script>";
        $stmt->close();
    } else {
        // Prepare the UPDATE query
        $query = "UPDATE therapist SET 
                    therapistName = ?, 
                    email = ?, 
                    address = ?, 
                    phone = ?, 
                    birthday = ? 
                  WHERE therapistID = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($query)) {
            // Bind the parameters to the query
            $stmt->bind_param("ssssss", $fullName, $email, $address, $phone, $birthDate, $therapist_id);

            // Execute the statement
            if ($stmt->execute()) {
                // Set the updated therapist name in the session
                $_SESSION['therapist_name'] = $fullName;

                // Success message and redirection with delay for confirmation
                echo "<script>
                        alert('Profile updated successfully!');
                        setTimeout(function() {
                            window.location.href = 'therapist-dashboard.php';
                        }, 800);
                      </script>";
                exit;
            } else {
                // Error message if update fails
                echo "<script>alert('Error updating profile: " . $stmt->error . "');</script>";
            }

            // Close the statement
            $stmt->close();
        } else {
            // If the query preparation fails
            echo "<script>alert('Error preparing the query: " . $conn->error . "');</script>";
        }
    }
} else {
    // Fetch the therapist's current data for pre-filling the form
    $query = "SELECT * FROM therapist WHERE therapistID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $therapist_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch therapist details
        $therapist = $result->fetch_assoc();

        // Split the therapist's full name into first and last name
        $fullName = $therapist['therapistName'];
        $nameParts = explode(' ', $fullName);
        $lastName = array_pop($nameParts); // Last name is the last part
        $firstName = implode(' ', $nameParts); // The rest is the first name

        // Extract birth date components (year, month, day)
        $birthYear = date("Y", strtotime($therapist['birthday']));
        $birthMonth = date("m", strtotime($therapist['birthday']));
        $birthDay = date("d", strtotime($therapist['birthday']));

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Therapist not found!";
        exit();
    }
}
?>
