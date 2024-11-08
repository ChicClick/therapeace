<?php
include 'db_conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve therapies and comments
    $therapies = isset($_POST['therapies']) ? implode(',', $_POST['therapies']) : '';
    $comments = isset($_POST['comments']) ? $_POST['comments'] : '';

    // Retrieve guestId from the form submission
    $guestId = isset($_POST['guestId']) ? $_POST['guestId'] : 0;

    // Prepare and execute the SQL query
    $sql = "UPDATE guest SET matchTherapy = ?, comments = ?, status = 2 WHERE GuestID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $therapies, $comments, $guestId);

    if ($stmt->execute()) {
        // Display a success message in a JavaScript alert
        echo "<script>
                alert('Form submitted successfully!');
                window.location.href = 'therapist-dashboard.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>