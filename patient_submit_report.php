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

// Get the logged-in patientID and therapistID from session
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

if ($checkResult->num_rows > 0) {
    $lastReport = $checkResult->fetch_assoc();
    $lastReportTime = strtotime($lastReport['created_at']);
    $twoWeeksInSeconds = 14 * 24 * 60 * 60; // 14 days in seconds
    $currentTime = time();

    if (!$testingMode && ($currentTime - $lastReportTime) < $twoWeeksInSeconds) {
        echo json_encode([
            'success' => false,
            'error' => 'A report request was already submitted for this therapist within the past 14 days. Please wait for it to be verified.'
        ]);
        exit();
    }
}

// Fetch session feedback notes for the patient and selected therapist
$sql = "SELECT feedback FROM sessionfeedbacknotes n
        JOIN sessions s ON n.sessionID = s.sessionID
        WHERE s.patientID = ? AND s.therapistID = ?";
$stmt = $conn->prepare($sql); // Use the existing $conn
$stmt->bind_param("ss", $patientID, $therapistID);
$stmt->execute();
$result = $stmt->get_result();

$feedbackText = "";
while ($row = $result->fetch_assoc()) {
    $feedbackText .= $row['feedback'] . "\n\n";
}

// Define categories and extract their content
$categories = [
    'General Considerations' => '',
    'Management Given' => '',
    'Observations and Improvements' => '',
    'Recommendations' => ''
];

// Regular expression to extract feedback for each category and combine them
foreach ($categories as $category => &$content) {
    // Match feedback for each category using regex
    if (preg_match("/{$category}:(.*?)(?=(?:\n\n[^\n]+:|$))/is", $feedbackText, $matches)) {
        $content = trim($matches[1]);
    } else {
        $content = "No specific feedback was provided for this category.";
    }

    // Split the content by feedback and add each feedback to the category
    $feedbackLines = array_filter(explode("\n", $content), function ($line) {
        return strlen($line) > 10 && !preg_match('/confidential support|samaritans|suicide prevention/i', $line);
    });

    // Combine the feedback lines for each category
    $categories[$category] = implode("\n", array_unique($feedbackLines));
}

// Now, combine all feedback for each category
$combinedFeedback = "";
foreach ($categories as $category => $content) {
    $combinedFeedback .= "{$category}:\n";
    $combinedFeedback .= $content . "\n\n";
}

// Define potential starting lines for the Recommendations category
$recommendationStartLines = [
    "Based on recent progress, it is recommended that the child continues to work on articulation using visual aids.",
    "To further improve speech clarity, it is suggested to focus on interactive exercises with flashcards.",
    "As the child has made significant progress in articulation, incorporating more structured speech activities is advised.",
    "It would be beneficial for the child to engage in additional speech exercises that emphasize breath control.",
    "Considering the improvement in the child’s attention span, it is recommended to introduce more complex verbal tasks."
];

// Function to generate random starting line for Recommendations
function getRandomRecommendationStart() {
    global $recommendationStartLines;
    return $recommendationStartLines[array_rand($recommendationStartLines)];
}

$apiKey = "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";
$url = "https://api-inference.huggingface.co/models/facebook/bart-large-cnn";

$headers = [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
];

// Define parameters for summarization (max and min lengths for different categories)
$parameters = [
    "General Considerations" => ["max_length" => 150, "min_length" => 30],
    "Management Given" => ["max_length" => 250, "min_length" => 40],
    "Observations and Improvements" => ["max_length" => 200, "min_length" => 40],
    "Recommendations" => ["max_length" => 120, "min_length" => 25]
];

$summaryText = "";
foreach ($categories as $category => $content) {
    // Log content for debugging purposes
    error_log("Category: $category, Content: $content");  // Log the content being sent

    // Ensure there's enough content for summarization, otherwise skip
    if (strlen(trim($content)) < 50 && $category !== "Recommendations") {
        $summaryText .= "{$category}:\nNo sufficient feedback to summarize.\n\n";
        continue;
    }

    if ($category == "Recommendations") {
        // For Recommendations, add a random starting sentence and append the feedback content
        $randomStart = getRandomRecommendationStart();
        
        // Summarize the feedback content for Recommendations if available
        $data = json_encode([
            "inputs" => $content,
            "parameters" => $parameters[$category]
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        $summary = "";

        if (isset($responseData[0]['summary_text'])) {
            $summary = $responseData[0]['summary_text'];
            // Clean up the text (optional, if needed)
            $summary = str_replace("throughout the session", "throughout the sessions", $summary);
            $summary = str_replace("during the session", "during the sessions", $summary);
            $summary = str_replace("in the session", "in the sessions", $summary);
        }

        // Combine the random start and summarized content for Recommendations
        $summaryText .= "Recommendations:\n" . $randomStart . " " . $summary . "\n\n";
    } else {
        // Normal summarization process for other categories
        $data = json_encode([
            "inputs" => $content,
            "parameters" => $parameters[$category]
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        if (isset($responseData[0]['summary_text'])) {
            $summary = $responseData[0]['summary_text'];
            $summary = str_replace("throughout the session", "throughout the sessions", $summary);
            $summary = str_replace("during the session", "during the sessions", $summary);
            $summary = str_replace("in the session", "in the sessions", $summary);
            $summaryText .= "{$category}:\n{$summary}\n\n";
        } else {
            $summaryText .= "{$category}:\nNo summarization available for this section.\n\n";
        }
    }
}

// Insert the summary into the database
$insertSQL = "INSERT INTO reports (patientID, therapistID, summary, status, created_at, updated_at, pdf_path)
              VALUES (?, ?, ?, 'Pending', NOW(), NOW(), '')
              ON DUPLICATE KEY UPDATE 
                summary = VALUES(summary),
                status = 'Pending',
                updated_at = NOW();";

$insertStmt = $conn->prepare($insertSQL);
$insertStmt->bind_param("sss", $patientID, $therapistID, $summaryText);
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
?>