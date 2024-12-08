<?php
include 'config.php';
include 'db_conn.php'; // Assumes $conn is established in db_conn.php

// Check if the patient is logged in
if (!isset($_SESSION['patientID'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Please log in to submit a report.'
    ]);
    exit();
}

// Get the logged-in patientID from session
$patientID = $_SESSION['patientID'];
$therapistID = isset($_POST['therapistID']) ? $_POST['therapistID'] : '';

if (empty($therapistID)) {
    echo json_encode([
        'success' => false,
        'error' => 'Therapist ID is required.'
    ]);
    exit();
}

// Check if there is a recent report request within the last week
$checkSQL = "SELECT created_at FROM reports 
             WHERE patientID = ? AND therapistID = ? 
             ORDER BY created_at DESC 
             LIMIT 1";
$checkStmt = $conn->prepare($checkSQL);
$checkStmt->bind_param("ss", $patientID, $therapistID);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

$testingMode = false; // Set to true for testing, false for production

// if ($checkResult->num_rows > 0) {
//     $lastReport = $checkResult->fetch_assoc();
//     $lastReportTime = strtotime($lastReport['created_at']);
//     $oneWeekInSeconds = 60 * 24 * 60 * 60; // 7 days in seconds
//     $currentTime = time();

//     if (!$testingMode && ($currentTime - $lastReportTime) < $oneWeekInSeconds) {
//         echo json_encode([
//             'success' => false,
//             'error' => 'A report request was already submitted for this therapist within the past week. Please wait for it to be verified.'
//         ]);
//         exit();
//     }
// }

// Fetch session feedback notes for the patient and selected therapist
$sql = "SELECT feedback FROM sessionfeedbacknotes n
        JOIN sessions s ON n.sessionID = s.sessionID
        WHERE s.patientID = ? AND s.therapistID = ?";
$stmt = $conn->prepare($sql); // Use the existing $conn
$stmt->bind_param("ss", $patientID, $therapistID);
$stmt->execute();
$result = $stmt->get_result();

$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row['feedback']; // Collect feedback notes
}
$feedbackText = implode(" ", $notes);

// Summarize the feedback notes using Hugging Face API
$apiKey = "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";
$url = "https://api-inference.huggingface.co/models/facebook/bart-large-cnn";

$data = json_encode(["inputs" => $feedbackText]);
$headers = [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode([
        'success' => false,
        'error' => 'Error connecting to summarization API.'
    ]);
    exit();
}

// Decode the response
$responseData = json_decode($response, true);
if (isset($responseData[0]['summary_text'])) {
    $summary = $responseData[0]['summary_text'];
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Error with API response: ' . json_encode($responseData)
    ]);
    exit();
}

// Prepare the SQL statement to insert the report
$insertSQL = "INSERT INTO reports (patientID, therapistID, summary, status, created_at, updated_at, pdf_path)
              VALUES (?, ?, ?, 'Pending', NOW(), NOW(), '')
              ON DUPLICATE KEY UPDATE 
                summary = VALUES(summary),
                status = 'Pending',
                updated_at = NOW();";

$insertStmt = $conn->prepare($insertSQL);
$insertStmt->bind_param("sss", $patientID, $therapistID, $summary);
$insertSuccess = $insertStmt->execute();

if ($insertSuccess) {
    echo json_encode([
        'success' => true,
        'message' => 'Report request submitted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Error submitting report: ' . $conn->error
    ]);
}

// Close statements
$stmt->close();
$insertStmt->close();
$checkStmt->close();
?>
