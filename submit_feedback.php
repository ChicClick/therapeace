<?php
include 'config.php';

// Check if the user is logged in (assumes patientID is stored in session)
session_start(); // Make sure the session is started
if (!isset($_SESSION['patientID'])) {
    echo "<p>You must be logged in to submit feedback.</p>";
    exit;
}

// Fetch parentID from the patient table based on the logged-in patient's patientID
$patientID = $_SESSION['patientID']; // Get logged-in patientID from session
$sql = "SELECT parentID FROM patient WHERE patientID = '$patientID'"; // Fetch parentID based on patientID
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Get the parentID from the query result
    $row = mysqli_fetch_assoc($result);
    $parentID = $row['parentID'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the data from the form
        $rating = $_POST['rating'];
        $feedback_text = mysqli_real_escape_string($conn, $_POST['feedback_text']);
        $consent = isset($_POST['consent']) ? 1 : 0; // Consent to display feedback

        // Insert the feedback into the database
        $sql = "INSERT INTO feedbacks (parentID, rating, feedback_text, consent)
                VALUES ('$parentID', '$rating', '$feedback_text', '$consent')";

        if (mysqli_query($conn, $sql)) {
            echo "<p>Thank you for your feedback!</p>";
            header("Location: patientHome.php"); // Redirect back to the home page after submission
            exit; // Make sure the script stops here
        } else {
            echo "<p>Error submitting feedback: " . mysqli_error($conn) . "</p>";
        }
    }
} else {
    echo "<p>Error: Parent information not found.</p>";
}
?>
