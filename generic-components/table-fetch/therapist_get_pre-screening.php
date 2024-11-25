<?php

include ('../../config.php');
include ('../../db_conn.php');

if (!isset($_SESSION['therapist_id'])) {
    http_response_code(401); // Unauthorized
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$mysqli = $conn;
$therapistID = $_SESSION['therapist_id'];

// Validate and get 'status' from query parameters
if (!isset($_GET['status']) || empty($_GET['status'])) {
    http_response_code(400); // Bad Request
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Missing or invalid status parameter.']);
    exit();
}

$status = $_GET['status'];

$sql = "
    SELECT
        g.GuestID,
        g.GuestName AS guest_name,
        g.ChildName AS child_name,
        g.matchTherapy as match_therapy,
        g.comments as comments,
        g.Age AS child_age,
        g.Email AS email,
        g.DateSubmitted AS date_submitted,
        g.status AS guest_status
    FROM
        guest g
    WHERE
        g.status = ?
";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to prepare statement.']);
    exit();
}

// Bind the 'status' parameter (assuming it's a string)
$stmt->bind_param("s", $status); // "s" indicates the parameter is a string

// Execute the query
if (!$stmt->execute()) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to execute statement.']);
    exit();
}

$result = $stmt->get_result();

// Fetch prescreening
$prescreening = [];

while ($row = $result->fetch_assoc()) {
    $prescreening[] = $row;
}

// Close the statement and database connection
$stmt->close();
$mysqli->close();

// Return JSON response
header("Content-Type: application/json");
echo json_encode($prescreening);
exit();
?>
