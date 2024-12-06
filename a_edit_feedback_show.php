<?php
include 'db_conn.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    $show = isset($data['show']) ? intval($data['show']) : null;
    $feedbackID = isset($data['feedbackID']) ? intval($data['feedbackID']) : null;

    if ($feedbackID === null || $show === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $sql = "UPDATE feedbacks SET `show` = ? WHERE feedbackID = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ii", $show, $feedbackID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Feedback updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
