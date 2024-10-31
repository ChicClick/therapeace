<?php
include 'db_conn.php'; // Include the database connection file

// Fetch all questi ons from the prescreening_questions table
$query = "SELECT * FROM prescreening_questions ORDER BY questionID";
$result = mysqli_query($conn, $query);

// Fetching the questions into an array
$questions = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Group questions by category
$groupedQuestions = [];
foreach ($questions as $question) {
    $groupedQuestions[$question['category']][] = $question;
}

// Close the database connection
$conn->close();
