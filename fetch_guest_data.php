<?php
include 'db_conn.php';

if (isset($_GET['guestID'])) {
    $guestID = (int)$_GET['guestID']; // Get the guest ID from the query string

    // SQL query to fetch guest data
    $sql = "SELECT GuestName AS guest_name, ChildName AS child_name, Age AS child_age FROM guest WHERE GuestID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $guestID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row); // Return the guest data as JSON
    } else {
        echo json_encode(['guest_name' => '', 'child_name' => '', 'child_age' => '']); // Return empty data if not found
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No guest ID provided']); // Error handling
}
?>
