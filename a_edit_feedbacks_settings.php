<?php
include 'db_conn.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    $date_start = isset($data['date_start']) ? $data['date_start'] : null;
    $date_end = isset($data['date_end']) ? $data['date_end'] : null;
    $minimum_rating = isset($data['minimum_rating']) ? intval($data['minimum_rating']) : null;

    if ($date_start === null || $date_end === null || $minimum_rating === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $sql = "UPDATE feedbacks_settings SET date_start = ?, date_end = ?, minimum_rating = ? WHERE id = 1";  // Assuming id = 1 for updating the record

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssi", $date_start, $date_end, $minimum_rating);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Feedback settings updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
