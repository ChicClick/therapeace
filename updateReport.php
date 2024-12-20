<?php
include 'db_conn.php';
require_once __DIR__ . '/fpdf/fpdf.php';
require_once __DIR__ . '/fpdi/src/autoload.php';
require 'vendor/autoload.php';

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use setasign\Fpdi\Fpdi;

date_default_timezone_set('Asia/Manila');

// AWS S3 Configuration
$bucketName = getenv('BUCKET_NAME');
$region = getenv('REGION');
$accessKey = getenv('ACCESS_KEY');
$secretKey = getenv('SECRET_ACCESS_KEY');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug incoming POST data
    var_dump($_POST);

    // Validate required fields
    if (!isset($_POST['reportID']) || !isset($_POST['summary'])) {
        die("Error: Missing required fields in POST request.");
    }
}

$reportID = $_POST['reportID'];
$summary = $_POST['summary']; // Treating as plain text; decode if JSON

// SQL query to fetch patient and therapist details based on report's patientID and therapistID
$query = "
    SELECT 
        p.patientName, 
        p.birthday AS patient_birthday, 
        p.gender AS patient_gender,
        s.serviceName,
        t.therapistName,
        r.patientID,  -- Add patientID from the reports table
        r.therapistID -- Add therapistID from the reports table
    FROM reports r
    JOIN patient p ON r.patientID = p.patientID
    JOIN therapist t ON r.therapistID = t.therapistID
    JOIN services s ON p.serviceID = s.serviceID
    WHERE r.reportID = ?
";

// Prepare and execute the SQL query to fetch the data for the reportID
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $reportID); // Bind the reportID
$stmt->execute();
$result = $stmt->get_result();

// Fetch the data for the patient and therapist
$data = $result->fetch_assoc();
if (!$data) {
    die("Error: Report data not found.");
}

// Fetch the necessary details
$patientName = $data['patientName'];
$patient_birthday = $data['patient_birthday'];
$patient_gender = $data['patient_gender'];
$serviceName = $data['serviceName'];
$therapistName = $data['therapistName'];

// Fetch the patientID and therapistID
$patientID = $data['patientID'];
$therapistID = $data['therapistID'];

// Calculate age from the patient's birthday
$age = date_diff(date_create($patient_birthday), date_create('today'))->y;

// Debug summary content
var_dump($summary);

$status = "verified";
$updated_at = date('Y-m-d H:i:s');

// Check if the template file exists
$templateFile = 'progressReportFinal.pdf';
if (!file_exists($templateFile)) {
    die("Error: Template PDF file not found.");
}

// Define the regex pattern for each category
$pattern = '/(General Considerations:.*?)(Management Given:.*?)(Observations and Improvements:.*?)(Recommendations:.*)/s';

// Use regex to match the categories and their content
preg_match($pattern, $summary, $matches);

// Now we have the content for each category
$generalConsiderations = isset($matches[1]) ? $matches[1] : '';
$managementGiven = isset($matches[2]) ? $matches[2] : '';
$observationsImprovements = isset($matches[3]) ? $matches[3] : '';
$recommendations = isset($matches[4]) ? $matches[4] : '';

// Set the categories in an array
$categories = [
    'General Considerations' => $generalConsiderations,
    'Management Given' => $managementGiven,
    'Observations and Improvements' => $observationsImprovements,
    'Recommendations' => $recommendations
];

$pdf = new Fpdi();

// First page
$pdf->AddPage();
$pdf->setSourceFile($templateFile);
$template = $pdf->importPage(1); // Import the first page
$pdf->useTemplate($template, 0, 0, 210); // Use the first page

// Set font and content for the first page
$pdf->SetFont('Arial', '', 10);
$y = 80; // Starting y-position

// Insert the header with patient and therapist details
$pdf->SetXY(37, 40);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 7, $patientName);

$pdf->SetXY(42, 46);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 7, $age . ' / ' . $patient_gender);

$pdf->SetXY(48, 52);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 7, date('F j, Y', strtotime($patient_birthday)));

$pdf->SetXY(41, 57.7);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 7, $serviceName);

$pdf->SetXY(44, 64);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 7, $therapistName);


// Insert categories for the first page
foreach ($categories as $category => $content) {
    // Display the bolded category name and then the content
    
    // Look for the category name in the content
    $categoryHeader = $category . ":";

    // If the category header is found, apply bold font to it
    if (strpos($content, $categoryHeader) !== false) {
        // Set bold font for category header
        $pdf->SetXY(30, $y);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell(150, 7, $categoryHeader);

        // Conditionally adjust the spacing for Recommendations
        if ($category == 'Recommendations') {
            // Apply spacing specific to Recommendations
            $y += 10; 
        } else {
            // Apply default spacing for other categories
            $y += 3;
        }

        // After category header, switch to normal font for the rest of the content
        $pdf->SetFont('Arial', '', 10);
        // Remove the category header from the content and print the remaining part
        $contentWithoutHeader = str_replace($categoryHeader, '', $content);
        // Adjust x-position for content
        $pdf->SetXY(30, $y); 
        $pdf->MultiCell(150, 7, $contentWithoutHeader);
    } else {
        // If no category header, just print the content normally
        $pdf->SetXY(30, $y);
        $pdf->MultiCell(150, 7, $content);
    }

    // Adjust position for the next category
    $y += 45;

    // Check if the next category is "Recommendations", and force a new page for it
    if ($category == 'Observations and Improvements' && isset($categories['Recommendations'])) {
        // Add new page for Recommendations category
        $pdf->AddPage();  // Create a new page
        $template = $pdf->importPage(2); // Import the second page
        $pdf->useTemplate($template, 0, 0, 210); 
        $y = 40;  // Reset y-position for the new page

        // Now add therapist name on the second page
        $pdf->SetXY(24, 205);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 7, $therapistName);

        // Define text based on the serviceName
        $serviceText = '';
        if ($serviceName === 'Occupational Therapy') {
            $serviceText = 'Occupational Therapist';
        } elseif ($serviceName === 'Speech Therapy') {
            $serviceText = 'Speech Therapist';
        } elseif ($serviceName === 'Physical Therapy') {
            $serviceText = 'Physical Therapist';
        }

        // Add the service-specific text below the therapistName
        $pdf->SetXY(24, 210);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(100, 7, $serviceText);
        }
}

// Comment out the code below and other var dump for testing of position. This will display the PDF in the browser.
// $pdf->Output(); 

// Output PDF as string
$pdfContent = $pdf->Output('', 'S');

// Upload PDF to S3
$s3Client = new S3Client([
    'version' => 'latest',
    'region' => $region,
    'credentials' => [
        'key' => $accessKey,
        'secret' => $secretKey,
    ],
    'http' => [
        'verify' => false,
    ],
]);

$keyName = 'report_' . $reportID . '.pdf';

try {
    $result = $s3Client->putObject([
        'Bucket' => $bucketName,
        'Key' => $keyName,
        'Body' => $pdfContent,
        'ACL' => 'private',
    ]);

    $s3Url = $s3Client->getObjectUrl($bucketName, $keyName);
    echo "File uploaded successfully. S3 URL: " . $s3Url . "\n";
} catch (AwsException $e) {
    die("Error uploading file: " . $e->getMessage());
}

// Update database with new summary and PDF path
$query = "UPDATE reports SET summary = ?, status = ?, updated_at = ?, pdf_path = ? WHERE reportID = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssssi", $summary, $status, $updated_at, $keyName, $reportID);

if ($stmt->execute()) {
    header("Location: therapist-dashboard.php?message=" . urlencode("Updated Successfully"));
    exit;
} else {
    header("Location: therapist-dashboard.php?message=" . urlencode("Error updating PDF"));
    exit;
}

$stmt->close();
$conn->close();
