<?php
include 'db_conn.php';
include 'config.php';

$guestID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$response = [];

if ($guestID > 0) {

    $sql = "
        SELECT pq.questionID, pq.category, pq.questionText, pq.options, pq.inputType, fa.answerText
        FROM prescreening_questions pq
        LEFT JOIN form_answers fa ON pq.questionID = fa.questionID
        LEFT JOIN guest g ON fa.guestID = g.guestID
        WHERE g.guestID = ?
        ORDER BY pq.category
    ";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $guestID);
        $stmt->execute();
        $result = $stmt->get_result();

        $questions = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Ensure options is a string; if null, default to an empty string
                $optionsArray = explode(',', $row['options'] ?? '');
        
                $questions[$row['category']][] = [
                    'questionID' => $row['questionID'],
                    'questionText' => $row['questionText'],
                    'options' => $optionsArray,
                    'inputType' => $row['inputType'],
                    'selectedAnswer' => $row['answerText'],
                ];
            }
        }        
        $response = [
            'status' => 'success',
            'data' => $questions,
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Failed to prepare the SQL statement.',
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid or missing guestID.',
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
