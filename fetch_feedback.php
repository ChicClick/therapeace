<?php
// Include database connection file
include 'db_conn.php';

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT feedback FROM sessionfeedbacknotes WHERE feedbackDate = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'feedback' => $row['feedback']]);
    } else {
        echo json_encode(['success' => false, 'feedback' => null]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'feedback' => null]);
}
?>