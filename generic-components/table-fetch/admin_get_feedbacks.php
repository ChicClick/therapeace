<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include ('../../config.php');
include ('../../db_conn.php');

//PROD PATH
// include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/db_conn.php';

if (!isset($_SESSION['username'])) {
    http_response_code(401); // Unauthorized
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$mysqli = $conn;

$feedbacks = [];
$sqlFeedbacks = "
     SELECT 
        f.`show`,
        f.feedbackID,
        f.parentID, 
        f.rating, 
        f.consent, 
        f.feedback_text,
        f.created_at
    FROM feedbacks f
    JOIN feedbacks_settings fs ON fs.id = 1  -- Join feedbacks_settings where ID = 1
    WHERE f.rating BETWEEN fs.minimum_rating AND 5  -- Rating filter between minimum_rating and 5
    AND f.created_at BETWEEN fs.date_start AND fs.date_end  -- Date filter between date_start and date_end
";

$stmt = $mysqli->prepare($sqlFeedbacks);

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => $mysqli->error]);
    exit();
}

$mysqli->close();

header("Content-Type: application/json");
echo json_encode($feedbacks);
exit();
?>
