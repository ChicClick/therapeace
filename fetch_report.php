<?php
include 'db_conn.php';

if (isset($_GET['reportID'])) {
    $reportID = $_GET['reportID'];
    
    $query = "SELECT summary, status FROM reports WHERE reportID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reportID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);  // Returns summary and status as JSON
    } else {
        echo json_encode(["summary" => "", "status" => ""]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
