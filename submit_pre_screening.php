<?php 
include 'config.php';
include 'db_conn.php';

// Fetch questions from the database
$sql = "SELECT questionID, questionText FROM prescreening_questions";
$result = $conn->query($sql);

// Create a dynamic question mapping
$questionMap = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questionMap["question_" . $row['questionID']] = $row['questionID'];
    }
}

// Add additional fields that are not part of the questions table
$additionalFields = [
    'age' => 2,  
    'sex' => 3, 
    'dob' => 4,  
    'mother_age' => 8,
    'father_age' => 9, 
    'labor_hours' => 13,  
    'siblings' => 23,  
    'sibling_position' => 24,  
];

$questionMap = array_merge($questionMap, $additionalFields);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capture email and phone from the form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $guestName = mysqli_real_escape_string($conn, $_POST['guestName']);

    // Initialize ChildName to an empty string
    $childName = '';

    // Loop through each question-answer pair to find the answer for questionID 1 (ChildName)
    foreach ($_POST as $questionName => $answer) {
        if (isset($questionMap[$questionName]) && $questionMap[$questionName] == 1) {
            // Capture ChildName from the answer associated with questionID 1
            $childName = is_array($answer) ? implode(', ', $answer) : $answer;
            $childName = mysqli_real_escape_string($conn, $childName);
            break;  // No need to keep looping once ChildName is found
        }
    }

    // Insert into the guest table with ChildName
    $guestSql = "INSERT INTO guest (Email, Phone, GuestName, ChildName) VALUES ('$email', '$phone', '$guestName', '$childName')";
    if (!$conn->query($guestSql)) {
        die("Error inserting guest details: " . $conn->error);
    }

    // Get the last inserted guestID
    $guestID = $conn->insert_id;
    $_SESSION['guestID'] = $guestID;
    $errors = [];

    // Calculate age if 'dob' is provided
    if (isset($_POST['dob'])) {
        $dob = $_POST['dob'];
        $dobDateTime = new DateTime($dob);
        $currentDate = new DateTime();
        $age = $currentDate->diff($dobDateTime)->y;  // Calculate age in years

        // Insert date of birth into form_answers table
        $dobQuestionID = $questionMap['dob'];
        $sanitizedDob = mysqli_real_escape_string($conn, $dob);
        $sql = "INSERT INTO form_answers (guestID, questionID, answerText) VALUES ('$guestID', '$dobQuestionID', '$sanitizedDob')";
        if (!$conn->query($sql)) {
            $errors[] = "Error inserting date of birth: " . $conn->error;
        }

        // Insert age into form_answers table
        $ageQuestionID = $questionMap['age'];
        $sql = "INSERT INTO form_answers (guestID, questionID, answerText) VALUES ('$guestID', '$ageQuestionID', '$age')";
        if (!$conn->query($sql)) {
            $errors[] = "Error inserting age: " . $conn->error;
        }
    }

    // Loop through each question-answer pair and insert into form_answers
    foreach ($_POST as $questionName => $answer) {
        if ($questionName === 'submit' || $questionName === 'dob') {
            continue;
        }

        if (isset($questionMap[$questionName])) {
            $questionID = $questionMap[$questionName];
        } else {
            continue;
        }

        if (is_array($answer)) {
            foreach ($answer as $value) {
                $sanitizedValue = mysqli_real_escape_string($conn, $value);
                $sql = "INSERT INTO form_answers (guestID, questionID, answerText) VALUES ('$guestID', '$questionID', '$sanitizedValue')";
                if (!$conn->query($sql)) {
                    $errors[] = "Error inserting answer for question ID $questionID: " . $conn->error;
                }
            }
        } else {
            $sanitizedAnswer = mysqli_real_escape_string($conn, $answer);
            $sql = "INSERT INTO form_answers (guestID, questionID, answerText) VALUES ('$guestID', '$questionID', '$sanitizedAnswer')";
            if (!$conn->query($sql)) {
                $errors[] = "Error inserting answer for question ID $questionID: " . $conn->error;
            }
        }
    }

    if (empty($errors)) {
        echo "<script>showModal($responseID);</script>";
    } else {
        echo "Errors occurred during form submission: " . implode(', ', $errors);
    }

    $conn->close();
    header("Location: guestPreScreening.php?sucess=1");
    exit();
} else {
    echo "Error inserting form response: " . $conn->error;
}
?>
