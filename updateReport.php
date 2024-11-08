<?php
include 'db_conn.php';
require_once __DIR__ . '/fpdf/fpdf.php'; // Adjust the path as needed
require_once __DIR__ . '/fpdi/src/autoload.php'; // Include FPDI
require 'vendor/autoload.php'; // Load AWS SDK

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use setasign\Fpdi\Fpdi;

// Set the correct timezone
date_default_timezone_set('Asia/Manila');

// Check if required POST data is provided
if (!isset($_POST['reportID']) || !isset($_POST['summary'])) {
    die("Error: Missing reportID or summary data.");
}

$reportID = $_POST['reportID'];
$summary = $_POST['summary'];
$status = "verified";
$updated_at = date('Y-m-d H:i:s'); // Current datetime

// Check if template exists
$templateFile = 'progressReport1.pdf'; // Ensure the correct path
if (!file_exists($templateFile)) {
    die("Error: Template PDF file not found.");
}

// Create the FPDI object (extends FPDF)
$pdf = new Fpdi();
$pdf->AddPage();

// Import the template (load the template without adding any text yet)
$pdf->setSourceFile($templateFile);
$template = $pdf->importPage(1); // Import the first page

// Use the template as the background
$pdf->useTemplate($template, 0, 0, 210); // A4 width

// Set font for the text
$pdf->SetFont('Arial', '', 12);

// Add text in specific positions
$pdf->SetXY(50, 80);  // X and Y are the coordinates on the template
$pdf->MultiCell(0, 10, $summary);  // Insert the summary text

// Generate a temporary file for storing the PDF
$tempPdfPath = 'temp_report_' . $reportID . '.pdf';
$pdf->Output($tempPdfPath, 'F'); // Save the PDF file to a temporary path

// Check if the PDF was created successfully
if (!file_exists($tempPdfPath)) {
    die("Error: PDF file was not created.");
}

// Instantiate an S3 client
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => getenv('AWS_DEFAULT_REGION'), // Use your configured region
    'credentials' => [
        'key'    => getenv('AWS_ACCESS_KEY_ID'),
        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    ],
]);

// Set your S3 bucket name
$bucketName = getenv('AWS_BUCKET_NAME'); // Assuming the bucket name is stored as an environment variable

// Upload the PDF to S3
$pdfKey = 'pdf_reports/report_' . $reportID . '.pdf'; // Key for the S3 object
try {
    $result = $s3->putObject([
        'Bucket' => $bucketName,
        'Key'    => $pdfKey,
        'SourceFile' => $tempPdfPath,
        'ACL'    => 'private', // Set to 'private' or 'public-read' as per your requirements
    ]);

    // Get the S3 URL (you can also use the S3 object key)
    $pdfUrl = $result['ObjectURL'];

    // Delete the temporary file
    unlink($tempPdfPath);

    echo "PDF uploaded successfully to S3: $pdfUrl <br>";

    // Update the database with the new values, including pdf_path (use S3 key instead of local path)
    $query = "UPDATE reports SET summary = ?, status = ?, updated_at = ?, pdf_path = ? WHERE reportID = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $summary, $status, $updated_at, $pdfKey, $reportID);

    // Execute and check if the update is successful
    if ($stmt->execute()) {
        echo "Report updated and PDF created successfully.";
    } else {
        echo "Error updating report: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} catch (AwsException $e) {
    // Handle AWS errors
    echo "Error uploading PDF to S3: " . $e->getMessage();
}

?>
