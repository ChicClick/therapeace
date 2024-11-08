<?php
include 'db_conn.php';
require_once __DIR__ . '/fpdf/fpdf.php'; // Adjust the path as needed
require_once __DIR__ . '/fpdi/src/autoload.php'; // Include FPDI

use setasign\Fpdi\Fpdi;

// Set the correct timezone
date_default_timezone_set('Asia/Manila');

// Debugging: Check if the form data is correctly passed
var_dump($_POST);  // Check if the data is being sent correctly
if (!isset($_POST['reportID']) || !isset($_POST['summary'])) {
    die("Error: Missing reportID or summary data.");
}

$reportID = $_POST['reportID'];
$summary = $_POST['summary'];
$status = "verified";
$updated_at = date('Y-m-d H:i:s'); // Current datetime

// Debugging: Check if data is coming through correctly
echo "reportID: $reportID <br> Summary: $summary <br>";

// Check if template exists
$templateFile = 'progressReport1.pdf'; // Ensure the correct path
if (!file_exists($templateFile)) {
    die("Error: Template PDF file not found.");
}

// Create the FPDI object (extends FPDF)
$pdf = new Fpdi();
$pdf->AddPage();

// Import the template (this should only load the template without adding any text yet)
$pdf->setSourceFile($templateFile);
$template = $pdf->importPage(1); // Import the first page

// Use the template as the background
$pdf->useTemplate($template, 0, 0, 210); // A4 width

// Set font for the text
$pdf->SetFont('Arial', '', 12);

// Add text in specific positions
$pdf->SetXY(50, 80);  // X and Y are the coordinates on the template
$pdf->MultiCell(0, 10, $summary);  // Insert the summary text

// Define the path where the PDF will be stored
$pdfPath = 'pdf_reports/report_' . $reportID . '.pdf'; // Ensure 'pdf_reports' directory exists and is writable
$pdf->Output($pdfPath, 'F'); // Save the PDF file to the server

// Check if the PDF was created successfully
if (!file_exists($pdfPath)) {
    die("Error: PDF file was not created.");
}

echo "PDF created successfully: $pdfPath <br>";

// Update the database with the new values, including pdf_path
$query = "UPDATE reports SET summary = ?, status = ?, updated_at = ?, pdf_path = ? WHERE reportID = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssssi", $summary, $status, $updated_at, $pdfPath, $reportID);

// Execute and check if the update is successful
if ($stmt->execute()) {
    echo "Report updated and PDF created successfully.";
} else {
    echo "Error updating report: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
