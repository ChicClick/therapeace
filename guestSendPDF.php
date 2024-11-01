<?php
session_start(); // Start the session
ob_start(); // Start output buffering

// Include necessary libraries
require_once __DIR__ . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/fpdi/src/autoload.php';
include 'db_conn.php';

use setasign\Fpdi\Tcpdf\Fpdi;

// Check if the source PDF exists
if (!file_exists('subjectiveChecklist.pdf')) {
    die('The checklist.pdf file does not exist.');
}

// Initiate FPDI (extending TCPDF)
$pdf = new Fpdi();
$pdf->SetPrintHeader(false); // Disable the header
$pdf->SetPrintFooter(false); // Disable the footer


// Import the existing checklist.pdf
$pageCount = $pdf->setSourceFile('subjectiveChecklist.pdf'); // Load the PDF file
$templateId = $pdf->importPage(1); // Import the first page
$pdf->AddPage(); // Add a new page to work on
$pdf->useTemplate($templateId, -5, 0); // Use the template for the PDF

// Define positions for each question in the PDF using IDs
$answerPositions = [
    1 => ['x' => 35, 'y' => 26.5],
    2 => ['x' => 51, 'y' => 31],
    3 => ['x' => 56, 'y' => 31],
    4 => ['x' => 46, 'y' => 35.5],
    5 => ['x' => 53, 'y' => 40],
    6 => ['x' => 158, 'y' => 40],
    8 => ['x' => 49, 'y' => 81],
    9 => ['x' => 49, 'y' => 85],
    10 => ['x' => 30, 'y' => 94],
    13 => ['x' => 113, 'y' => 77],
    20 => ['x' => 80, 'y' => 141],
    21 => ['x' => 80, 'y' => 151],
    22 => ['x' => 80, 'y' => 163],
    23 => ['x' => 135, 'y' => 181.5],
    24 => ['x' => 160   , 'y' => 239],
    25 => ['x' => 50, 'y' => 186],
    26 => ['x' => 141   , 'y' => 186],
    27 => ['x' => 52, 'y' => 181.5],
    32 => ['x' => 31, 'y' => 35],
    33 => ['x' => 31, 'y' => 65],
    34 => ['x' => 31, 'y' => 95],
    35 => ['x' => 31, 'y' => 193],
    36 => ['x' => 31, 'y' => 224],
    37 => ['x' => 59, 'y' => 30],
    38 => ['x' => 59, 'y' => 38],
    39 => ['x' => 59, 'y' => 45],
    40 => ['x' => 59, 'y' => 72],
    41 => ['x' => 59, 'y' => 114],
    42 => ['x' => 59, 'y' => 157],
    43 => ['x' => 145, 'y' => 30],
    44 => ['x' => 145, 'y' => 38],
    45 => ['x' => 145, 'y' => 114],
    

];


// Define positions for multiple-choice options
$optionPositions = [
    7 => [
        'Planned' => ['x' => 26, 'y' => 68.5],
        'Unwanted' => ['x' => 26, 'y' => 72.5],
    ],
    11 => [
        'Diabetic' => ['x' => 37, 'y' => 111],
        'Hypertensive' => ['x' => 37, 'y' => 115.5],
        'Smoke' => ['x' => 37, 'y' => 120],
        'Alcoholic Drinker' => ['x' => 37, 'y' => 124],
        'Miscarriage/Pregnancy Loss' => ['x' => 37, 'y' => 128],
    ],
    12 => [
        'CS Delivery' => ['x' => 83, 'y' => 68.5],
        'Normal Vaginal Delivery' => ['x' => 83, 'y' => 72.5],
    ],
    14 => [
        'Full-term (>37 weeks)' => ['x' => 83, 'y' => 81],
        'Pre-term (<37 weeks)' => ['x' => 83, 'y' => 85],
    ],
    15 => [
        'Cord coil' => ['x' => 95, 'y' => 94],
        'Pre-eclampsia' => ['x' => 95, 'y' => 98],
        'Infection' => ['x' => 95, 'y' => 102.5],
        'High blood pressure' => ['x' => 95, 'y' => 107],
        'Abnormal heart rate' => ['x' => 95, 'y' => 111],
        'Vaginal bleeding' => ['x' => 95, 'y' => 115.5],
        'Early water break' => ['x' => 95, 'y' => 119.5],
        'Lack of oxygen' => ['x' => 95, 'y' => 123.5],
        'Others' => ['x' => 95, 'y' => 128],
    ],
    17 => [
        'Hand-foot-mouth disease' => ['x' => 153, 'y' => 81],
        'Flu' => ['x' => 153, 'y' => 89.5],
        'Ear infection' => ['x' => 153, 'y' => 94],
    ],
    18 => [
        'pneumonia' => ['x' => 153, 'y' => 102.5],
        'dengue' => ['x' => 153, 'y' => 107],
        'others' => ['x' => 153, 'y' => 111],
    ],
    19 => [
        'occupational therapy' => ['x' => 153, 'y' => 124],
        'physical therapy' => ['x' => 153, 'y' => 128],
        'speech therapy' => ['x' => 153, 'y' => 132.5],
    ],
    28 => [
        'Stand-alone House' => ['x' => 29, 'y' => 209],
        'Apartment/Condo' => ['x' => 69, 'y' => 209],
    ],
    29 => [
        '1-floor' => ['x' => 29, 'y' => 202],
        '2-floor' => ['x' => 69, 'y' => 202],
    ],
    30 => [
        'Maluwag o sapat ang space' => ['x' => 29, 'y' => 216],
        'May hangin o maayos na ventilation' => ['x' => 69, 'y' => 216],
        'May ilaw o electricity' => ['x' => 29, 'y' => 227],
        'May mga laruan sa bahay' => ['x' => 69, 'y' => 227],
        'Malapit sa pamilihan' => ['x' => 29, 'y' => 239],
        'Malapit sa ospital' => ['x' => 69, 'y' => 239],
    ],
    31 => [
        'Parents (Mother/Father)' => ['x' => 131, 'y' => 213.3],
        'Siblings' => ['x' => 131, 'y' => 217.5],
        'Neighbors' => ['x' => 131, 'y' => 222],
        'Cousins' => ['x' => 131, 'y' => 226.5],
    ],

];

// Define custom width for text questions
$customWidths = [
    32 => 155,    
    33 => 155,   
    34 => 155,   
    35 => 155,   
    36 => 155,   
    37 => 50,    
    38 => 50,    
    39 => 50,    
    40 => 50,    
    41 => 50,    
    42 => 50,    
    43 => 58,    
    44 => 58,    
    45 => 58,   
];

 // Handle POST request to get guest details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve guest details from POST data
    $guestName = $_POST['guestName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $submissionDate = date('Y-m-d H:i:s'); // Current date and time

    // Sanitize inputs
    $guestName = mysqli_real_escape_string($conn, $guestName);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);

    // Check if guest already exists in the guest table by email
    $checkGuestSql = "SELECT GuestID FROM guest WHERE Email='$email' LIMIT 1";
    $result = $conn->query($checkGuestSql);

    if ($result->num_rows > 0) {
        // Guest exists, update their info
        $row = $result->fetch_assoc();
        $guestID = $row['GuestID'];
        $updateGuestSql = "UPDATE guest SET GuestName='$guestName', DateSubmitted='$submissionDate' WHERE GuestID='$guestID'";
        if ($conn->query($updateGuestSql) === FALSE) {
            die("Error updating guest: " . $conn->error);
        }
    } else {
        // Guest doesn't exist, insert a new guest
        $insertGuestSql = "INSERT INTO guest (GuestName, Email, DateSubmitted) VALUES ('$guestName', '$email', '$submissionDate')";
        if ($conn->query($insertGuestSql) === TRUE) {
            $guestID = $conn->insert_id; // Get the new GuestID
        } else {
            die("Error inserting guest: " . $conn->error);
        }
    }

    // Insert the form response
    $insertResponseSql = "INSERT INTO form_responses (submissionDate, guestName, email, phone) VALUES ('$submissionDate', '$guestName', '$email', '$phone')";
    if ($conn->query($insertResponseSql) === FALSE) {
        die("Error inserting response: " . $conn->error);
    }
}

    } else {
        die("No response ID found in session.");
    }
} else {
    die("Invalid request method. Only POST requests are allowed.");
}

// Check if the responseID is set in session before proceeding to fetch data
if (!isset($_SESSION['responseID'])) {
    die("No response ID found in session. Current session data: " . print_r($_SESSION, true));
}

// Get the responseID from session
$responseID = $_SESSION['responseID'];

// Fetch form answers from the database
$sql = "SELECT q.questionID, q.questionText, a.answerText 
        FROM form_answers a 
        JOIN prescreening_questions q ON a.questionID = q.questionID 
        WHERE a.responseID = '$responseID'
        ORDER BY q.pageNumber, q.questionID";  // Fetch questions ordered by page number

$result = $conn->query($sql);

// Initialize an array to store the form answers
$formData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Store questionID as key and answer as value
        // Handle multiple answers as a comma-separated string
        $formData[$row['questionID']][] = $row['answerText']; // Use an array to store multiple answers
    }
} else {
    die('No form answers found for this response.');
}

// Fetch all questions and their corresponding page numbers from the database
$sqlQuestions = "SELECT questionID, questionText, pageNumber FROM prescreening_questions ORDER BY questionID"; // Ensure you're also fetching pageNumber
$resultQuestions = $conn->query($sqlQuestions);

if ($resultQuestions && $resultQuestions->num_rows > 0) {
    $pageQuestions = [];

    while ($row = $resultQuestions->fetch_assoc()) {
        $questionID = $row['questionID'];
        $questionText = $row['questionText'];
        $pageNumber = $row['pageNumber']; // Get the page number from the database

        // Assign questions to the appropriate page
        $pageQuestions[$pageNumber][$questionID] = $questionText;
    }
} else {
    die('No questions found in the database.');
}

// Loop through each question page
foreach ($pageQuestions as $pageNo => $questions) {
    // If you're dealing with multiple pages, you can repeat the import process here for each page if necessary
    if ($pageNo > 1) { // Assuming you have more than one page
        $templateId = $pdf->importPage($pageNo); // Import subsequent pages
        $pdf->AddPage();
        $pdf->useTemplate($templateId, -5, 0); // Use the template for the PDF
    }

    $pdf->SetFont('DejaVuSans', '', 9); // Set font

    // Check if there are specific questions for this page
    foreach ($questions as $questionID => $questionText) {
        if (isset($formData[$questionID])) {
            $answers = $formData[$questionID];

            // Check if the answer has specific positions defined
            if (isset($optionPositions[$questionID])) {
                // Iterate through each answer for this question
                foreach ($answers as $answer) {
                    if (isset($optionPositions[$questionID][$answer])) {
                        // Get the position for the specific answer
                        $xPosition = $optionPositions[$questionID][$answer]['x'];
                        $yPosition = $optionPositions[$questionID][$answer]['y'];

                        // Set position and write the checkmark
                        $pdf->SetXY($xPosition - 5, $yPosition); // Position slightly to the left
                        $pdf->Write(0, "✓"); // Use the checkmark character
                    }
                }
            }

    
            // Handle standard answer positions with possible custom width
            elseif (isset($answerPositions[$questionID])) {
                $xPosition = $answerPositions[$questionID]['x'];
                $yPosition = $answerPositions[$questionID]['y'];

                // Check if a custom width is defined for this question; otherwise, use default width
                $width = $customWidths[$questionID] ?? 50; // Default width if no custom width is set

                // Join answers for questions that might have multiple answers
                $answerText = implode(', ', $answers);

                // Add the text with either the custom width or default width
                $pdf->SetXY($xPosition, $yPosition);
                $pdf->MultiCell($width, 5, $answerText, 0, 'L');
            }
        }
    }
}

ob_end_clean(); // Clean output buffer


$pdf->Output('generated.pdf', 'D');

?>
